<?php
require_once 'config.php';

class ForwardChaining
{
    private $conn;
    private $facts = [];
    private $recommendations = [];
    private $additional_calories = 0;

    public function __construct()
    {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function setFacts($facts)
    {
        $this->facts = $facts;
    }

    public function execute()
    {
        // Reset recommendations
        $this->recommendations = [
            'rekomendasi_suplemen' => [],
            'rekomendasi_gizi' => [],
            'total_kalori' => 0,
            'total_protein' => 0,
            'catatan_khusus' => []
        ];

        $this->additional_calories = 0;

        try {
            // Get active rules
            $query = "SELECT * FROM aturan_forward_chaining WHERE is_active = 1 ORDER BY id";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            $rules = $stmt->fetchAll(PDO::FETCH_ASSOC);

            error_log("Executing forward chaining with " . count($rules) . " rules");

            foreach ($rules as $rule) {
                $condition_result = $this->evaluateCondition($rule['kondisi']);
                error_log("Rule {$rule['kode_aturan']}: {$rule['kondisi']} = " . ($condition_result ? 'TRUE' : 'FALSE'));

                if ($condition_result) {
                    $this->executeAction($rule['aksi'], $rule['nama_aturan']);
                }
            }

            // Apply additional calories from rules
            $this->recommendations['total_kalori'] += $this->additional_calories;

            error_log("Final recommendations: " . json_encode($this->recommendations));
        } catch (Exception $e) {
            error_log("Error in forward chaining execute: " . $e->getMessage());
            throw $e;
        }

        return $this->recommendations;
    }

    private function evaluateCondition($condition)
    {
        if (empty($condition)) {
            return false;
        }

        $evaluation_condition = $condition;

        // Replace variables with actual values
        foreach ($this->facts as $key => $value) {
            // Handle string values - wrap in quotes
            if (is_string($value)) {
                $evaluation_condition = str_replace($key, "'" . addslashes($value) . "'", $evaluation_condition);
            } else {
                $evaluation_condition = str_replace($key, $value, $evaluation_condition);
            }
        }

        // Handle LIKE conditions for string matching
        $evaluation_condition = preg_replace_callback('/(\w+)\s+LIKE\s+"%(.*?)%"/', function ($matches) {
            $field = $matches[1];
            $search = $matches[2];

            if (isset($this->facts[$field])) {
                $field_value = $this->facts[$field];
                $result = stripos($field_value, $search) !== false;
                error_log("LIKE condition: field='$field', search='$search', value='$field_value', result=" . ($result ? 'true' : 'false'));
                return $result ? 'true' : 'false';
            }
            return 'false';
        }, $evaluation_condition);

        // Handle basic comparisons
        $evaluation_condition = str_replace(' = ', ' == ', $evaluation_condition);
        $evaluation_condition = str_replace(' AND ', ' && ', $evaluation_condition);
        $evaluation_condition = str_replace(' OR ', ' || ', $evaluation_condition);

        // Clean up any remaining issues
        $evaluation_condition = trim($evaluation_condition);

        error_log("Final evaluation condition: " . $evaluation_condition);

        // Evaluate the condition safely
        try {
            // Use a safer evaluation method
            $result = $this->safeEval($evaluation_condition);
            error_log("Evaluation result: " . ($result ? 'TRUE' : 'FALSE'));
            return $result;
        } catch (Exception $e) {
            error_log("Error evaluating condition: {$evaluation_condition} - " . $e->getMessage());
            return false;
        }
    }

    private function safeEval($condition)
    {
        // Very simple and safe evaluation for basic conditions
        if ($condition === 'true') return true;
        if ($condition === 'false') return false;

        // Handle basic comparisons
        if (preg_match('/^(.+)\s*==\s*(.+)$/', $condition, $matches)) {
            $left = trim($matches[1], " '");
            $right = trim($matches[2], " '");
            return $left == $right;
        }

        // Handle greater than
        if (preg_match('/^(.+)\s*>\s*(.+)$/', $condition, $matches)) {
            $left = floatval(trim($matches[1]));
            $right = floatval(trim($matches[2]));
            return $left > $right;
        }

        // Handle less than
        if (preg_match('/^(.+)\s*<\s*(.+)$/', $condition, $matches)) {
            $left = floatval(trim($matches[1]));
            $right = floatval(trim($matches[2]));
            return $left < $right;
        }

        // Handle AND conditions
        if (strpos($condition, '&&') !== false) {
            $parts = explode('&&', $condition);
            foreach ($parts as $part) {
                if (!$this->safeEval(trim($part))) {
                    return false;
                }
            }
            return true;
        }

        // Handle OR conditions
        if (strpos($condition, '||') !== false) {
            $parts = explode('||', $condition);
            foreach ($parts as $part) {
                if ($this->safeEval(trim($part))) {
                    return true;
                }
            }
            return false;
        }

        return false;
    }

    private function executeAction($action, $rule_name = '')
    {
        if (empty($action)) return;

        $actions = explode(';', $action);
        foreach ($actions as $act) {
            $act = trim($act);
            if (empty($act)) continue;

            if (strpos($act, '=') !== false) {
                list($key, $value) = explode('=', $act, 2);
                $key = trim($key);
                $value = trim($value, ' "\'');

                $this->processAction($key, $value, $rule_name);
            }
        }
    }

    private function processAction($key, $value, $rule_name)
    {
        error_log("Processing action: $key = $value from rule: $rule_name");

        switch ($key) {
            case 'rekomendasi_suplemen':
                if (!in_array($value, $this->recommendations['rekomendasi_suplemen'])) {
                    $this->recommendations['rekomendasi_suplemen'][] = $value;
                }
                break;

            case 'tambahan_kalori':
                $this->additional_calories += intval($value);
                break;

            case 'rekomendasi_protein':
            case 'rekomendasi_sayur_buah':
            case 'rekomendasi_cairan':
            case 'rekomendasi_frekuensi':
                if (!in_array($value, $this->recommendations['rekomendasi_gizi'])) {
                    $this->recommendations['rekomendasi_gizi'][] = $value;
                }
                break;

            default:
                // Handle other rekomendasi_* patterns
                if (strpos($key, 'rekomendasi_') === 0) {
                    if (!in_array($value, $this->recommendations['rekomendasi_gizi'])) {
                        $this->recommendations['rekomendasi_gizi'][] = $value;
                    }
                }
                break;
        }
    }

    public function calculateBaseNeeds($data_diri, $pola_makan)
    {
        try {
            // Default base values
            $base_calorie = 2000;
            $base_protein = 50;

            // Adjust based on current weight if available
            if (isset($data_diri['berat_badan_sekarang'])) {
                $current_weight = floatval($data_diri['berat_badan_sekarang']);
                if ($current_weight < 50) {
                    $base_calorie = 1800;
                } elseif ($current_weight > 70) {
                    $base_calorie = 2200;
                }
                $base_protein = max(50, $current_weight * 1.2);
            }

            // Pregnancy adjustments
            $trimester = isset($data_diri['trimester']) ? $data_diri['trimester'] : 2;
            $kalori_tambahan = ($trimester == 1) ? 180 : 300;
            $protein_tambahan = 17;

            $this->recommendations['total_kalori'] = $base_calorie + $kalori_tambahan;
            $this->recommendations['total_protein'] = $base_protein + $protein_tambahan;

            error_log("Base needs calculated - Kalori: {$this->recommendations['total_kalori']}, Protein: {$this->recommendations['total_protein']}");
        } catch (Exception $e) {
            error_log("Error in calculateBaseNeeds: " . $e->getMessage());
            // Set default values in case of error
            $this->recommendations['total_kalori'] = 2300;
            $this->recommendations['total_protein'] = 67;
        }

        return $this->recommendations;
    }

    public function generateSpecialNotes($kondisi_kesehatan)
    {
        $notes = [];

        try {
            if (!is_array($kondisi_kesehatan)) {
                return $notes;
            }

            // Check for special conditions and generate notes
            if (isset($kondisi_kesehatan['riwayat_penyakit']) && stripos($kondisi_kesehatan['riwayat_penyakit'], 'Anemia') !== false) {
                $notes[] = "⚠️ Kondisi anemia terdeteksi. Pastikan konsumsi suplemen zat besi secara teratur.";
            }

            if (isset($kondisi_kesehatan['kondisi_khusus'])) {
                if (stripos($kondisi_kesehatan['kondisi_khusus'], 'Hamil Kembar') !== false) {
                    $notes[] = "👶 Kehamilan kembar membutuhkan pemantauan ekstra dan asupan gizi lebih tinggi.";
                }

                if (stripos($kondisi_kesehatan['kondisi_khusus'], 'Usia di atas 35 tahun') !== false) {
                    $notes[] = "🎗️ Ibu hamil di atas 35 tahun memerlukan pemantauan kesehatan yang lebih ketat.";
                }

                if (stripos($kondisi_kesehatan['kondisi_khusus'], 'Diabetes Gestasional') !== false) {
                    $notes[] = "🩺 Diabetes gestasional terdeteksi. Perlu pengaturan diet khusus.";
                }

                if (stripos($kondisi_kesehatan['kondisi_khusus'], 'Vegetarian') !== false) {
                    $notes[] = "🌱 Pola makan vegetarian membutuhkan perhatian khusus pada kecukupan protein dan zat besi.";
                }
            }

            // Check for allergies
            if (isset($kondisi_kesehatan['alergi_makanan']) && !empty($kondisi_kesehatan['alergi_makanan'])) {
                $notes[] = "🚫 Terdeteksi alergi makanan: " . $kondisi_kesehatan['alergi_makanan'];
            }

            if (isset($kondisi_kesehatan['alergi_suplemen']) && !empty($kondisi_kesehatan['alergi_suplemen'])) {
                $notes[] = "💊 Terdeteksi alergi suplemen: " . $kondisi_kesehatan['alergi_suplemen'];
            }
        } catch (Exception $e) {
            error_log("Error in generateSpecialNotes: " . $e->getMessage());
        }

        return $notes;
    }
}
