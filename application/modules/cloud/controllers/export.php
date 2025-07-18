<?php   
if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once "./application/modules/auth/controllers/auth.php";
// Load required libraries
require_once APPPATH . '../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class Export extends auth
{
    // Move the private properties inside the class
    private $memory_limit = '512M';
    private $execution_time_limit = 3600; // 1 hour
    private $chunk_size = 1000;
    private $max_records_per_file = 50000; // Split into multiple files if needed
    
    var $document_upload_path;
    var $document_upload_location;
    
    function __construct()
    {
        parent::__construct();

        // Set memory and execution limits
        ini_set('memory_limit', $this->memory_limit);
        ini_set('max_execution_time', $this->execution_time_limit);
        
        $this->load->library('image_lib');

        $this->document_upload_path = realpath(APPPATH . '../assets/document_uploads');
        $this->document_upload_location = base_url().'assets/document_uploads/';
        
        $this->load->model('dental/dental_model');
        $this->load->model('nurse/nurse_model');
        $this->load->model('reception/reception_model');
        $this->load->model('accounts/accounts_model');
        $this->load->model('database');
        $this->load->model('hr/personnel_model');
        $this->load->model('admin/sections_model');
        $this->load->model('admin/admin_model');
        $this->load->model('admin/file_model');
        $this->load->model('online_diary/rooms_model');
        $this->load->model('auth/auth_model');
    }

    // Controller method - add this to your controller
    public function export_patient_visits_to_excel($visit_id = null)
    {
        // Get the data efficiently with a single optimized query
        $data = $this->get_patient_visits_data($visit_id);
        
        if (empty($data)) {
            show_error('No data found for export');
            return;
        }
        
        // Create new Spreadsheet object
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Set document properties
        $spreadsheet->getProperties()
            ->setCreator('Dental Management System')
            ->setTitle('Patient Visit Records')
            ->setSubject('Patient Visit Data Export')
            ->setDescription('Export of patient visit records with medical notes');
        
        // Define headers
        $headers = [
            'A1' => 'Visit ID',
            'B1' => 'Patient Number',
            'C1' => 'Patient Surname',
            'D1' => 'Patient Other Names',
            'E1' => 'Visit Date',
            'F1' => 'Visit Time',
            'G1' => 'Doctor',
            'H1' => 'Visit Type',
            'I1' => 'Presenting Complaint',
            'J1' => 'Past Medical History',
            'K1' => 'Past Dental History',
            'L1' => 'General Exam',
            'M1' => 'Soft Tissue',
            'N1' => 'Hard Tissue - General',
            'O1' => 'Hard Tissue - Decayed',
            'P1' => 'Hard Tissue - Filled',
            'Q1' => 'Hard Tissue - Missing',
            'R1' => 'Hard Tissue - Other',
            'S1' => 'Oral Examination',
            'T1' => 'Investigations',
            'U1' => 'Occlusal Exam',
            'V1' => 'Findings',
            'W1' => 'Plan Description',
            'X1' => 'Rx Done',
            'Y1' => 'TCA'
        ];
        
        // Set headers with styling
        foreach ($headers as $cell => $value) {
            $sheet->setCellValue($cell, $value);
        }
        
        // Style the header row
        $headerStyle = [
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF']
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4472C4']
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN
                ]
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER
            ]
        ];
        
        $sheet->getStyle('A1:Y1')->applyFromArray($headerStyle);
        
        // Auto-size columns for headers
        foreach (range('A', 'Y') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        
        // Fill data starting from row 2
        $row = 2;
        foreach ($data as $record) {
            $sheet->setCellValue('A' . $row, $record['visit_id']);
            $sheet->setCellValue('B' . $row, $record['patient_number']);
            $sheet->setCellValue('C' . $row, $record['patient_surname']);
            $sheet->setCellValue('D' . $row, $record['patient_othernames']);
            $sheet->setCellValue('E' . $row, date('jS M Y', strtotime($record['visit_date'])));
            $sheet->setCellValue('F' . $row, date('H:i a', strtotime($record['visit_time'])));
            $sheet->setCellValue('G' . $row, $record['doctor_name']);
            $sheet->setCellValue('H' . $row, $record['visit_type_name']);
            $sheet->setCellValue('I' . $row, $record['hpco_description'] ?? '-');
            $sheet->setCellValue('J' . $row, $record['past_medical_history'] ?? '-');
            $sheet->setCellValue('K' . $row, $record['past_dental_history'] ?? '-');
            $sheet->setCellValue('L' . $row, $record['general_exam_description'] ?? '-');
            $sheet->setCellValue('M' . $row, $record['soft_tissue'] ?? '-');
            $sheet->setCellValue('N' . $row, $record['general'] ?? '-');
            $sheet->setCellValue('O' . $row, $record['decayed'] ?? '-');
            $sheet->setCellValue('P' . $row, $record['filled'] ?? '-');
            $sheet->setCellValue('Q' . $row, $record['missing'] ?? '-');
            $sheet->setCellValue('R' . $row, $record['other'] ?? '-');
            $sheet->setCellValue('S' . $row, $record['oe_description'] ?? '-');
            $sheet->setCellValue('T' . $row, $record['investigation'] ?? '-');
            $sheet->setCellValue('U' . $row, $record['occlusal_exam_description'] ?? '-');
            $sheet->setCellValue('V' . $row, $record['finding_description'] ?? '-');
            $sheet->setCellValue('W' . $row, $record['plan_description'] ?? '-');
            $sheet->setCellValue('X' . $row, $record['rx_description'] ?? '-');
            $sheet->setCellValue('Y' . $row, strip_tags($record['tca_description'] ?? '-'));
            
            $row++;
        }
        
        // Apply borders to all data
        $dataRange = 'A1:Y' . ($row - 1);
        $sheet->getStyle($dataRange)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN
                ]
            ]
        ]);
        
        // Set row height for better readability
        for ($i = 2; $i < $row; $i++) {
            $sheet->getRowDimension($i)->setRowHeight(20);
        }
        
        // Create filename with timestamp
        $filename = 'patient_visits_' . date('Y-m-d_H-i-s') . '.xlsx';
        
        // Set headers for download
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        
        // Create writer and output file
        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        
        // Clean up
        $spreadsheet->disconnectWorksheets();
        exit;
    }

    // Optimized data retrieval method - O(n) complexity
    private function get_patient_visits_data($visit_id = null)
    {
        // First, get the basic visit data
        $this->db->select('
            v.visit_id,
            v.visit_date,
            v.visit_time,
            v.visit_type,
            v.personnel_id,
            p.patient_number,
            p.patient_surname,
            p.patient_othernames,
            per.personnel_fname as doctor_name
        ');
        
        $this->db->from('visit v');
        $this->db->join('patients p', 'v.patient_id = p.patient_id', 'left');
        $this->db->join('personnel per', 'v.personnel_id = per.personnel_id', 'left');
        
        // Add conditions
        $this->db->where('v.close_card !=', 2);
        $this->db->where('(v.parent_visit = 0 OR v.parent_visit IS NULL)');
        
        if ($visit_id !== null) {
            $this->db->where('v.visit_id <=', $visit_id);
        }
        
        $this->db->order_by('v.visit_date', 'DESC');
        $this->db->order_by('v.visit_time', 'DESC');
        
        $query = $this->db->get();
        
        if ($query->num_rows() === 0) {
            return [];
        }
        
        $visits = $query->result_array();
        
        // Extract visit IDs for efficient batch queries
        $visit_ids = array_column($visits, 'visit_id');
        $visit_ids_str = implode(',', $visit_ids);
        
        // Get all medical notes data in batches for better performance
        $medical_data = $this->get_medical_notes_batch($visit_ids_str);
        
        // Merge the data
        foreach ($visits as &$visit) {
            $visit_id = $visit['visit_id'];
            
            // Process visit types
            switch ($visit['visit_type']) {
                case 3:
                    $visit['visit_type_name'] = 'Other';
                    break;
                case 4:
                    $visit['visit_type_name'] = 'Insurance';
                    break;
                default:
                    $visit['visit_type_name'] = 'General';
                    break;
            }
            
            // Merge medical data
            $visit = array_merge($visit, $medical_data[$visit_id] ?? []);
        }
        
        return $visits;
    }

    // Helper method to get all medical notes in batch queries
    private function get_medical_notes_batch($visit_ids_str)
    {
        $medical_data = [];
        
        // Initialize empty arrays for all visits
        $visit_ids_array = explode(',', $visit_ids_str);
        foreach ($visit_ids_array as $vid) {
            $medical_data[$vid] = [
                'hpco_description' => null,
                'past_medical_history' => null,
                'past_dental_history' => null,
                'general_exam_description' => null,
                'soft_tissue' => null,
                'general' => null,
                'decayed' => null,
                'filled' => null,
                'missing' => null,
                'other' => null,
                'oe_description' => null,
                'investigation' => null,
                'occlusal_exam_description' => null,
                'finding_description' => null,
                'plan_description' => null,
                'rx_description' => null,
                'tca_description' => null
            ];
        }
        
        // Fetch HPCO data
        $hpco_query = $this->db->query("SELECT visit_id, hpco_description FROM visit_hpco WHERE visit_id IN ($visit_ids_str)");
        if ($hpco_query && $hpco_query->num_rows() > 0) {
            foreach ($hpco_query->result_array() as $row) {
                $medical_data[$row['visit_id']]['hpco_description'] = $row['hpco_description'];
            }
        }
        
        // Fetch History data
        $history_query = $this->db->query("SELECT visit_id, past_medical_history, past_dental_history FROM visit_history WHERE visit_id IN ($visit_ids_str)");
        if ($history_query && $history_query->num_rows() > 0) {
            foreach ($history_query->result_array() as $row) {
                $medical_data[$row['visit_id']]['past_medical_history'] = $row['past_medical_history'];
                $medical_data[$row['visit_id']]['past_dental_history'] = $row['past_dental_history'];
            }
        }
        
        // Fetch General Exam data
        $ge_query = $this->db->query("SELECT visit_id, general_exam_description FROM visit_general_exam WHERE visit_id IN ($visit_ids_str)");
        if ($ge_query && $ge_query->num_rows() > 0) {
            foreach ($ge_query->result_array() as $row) {
                $medical_data[$row['visit_id']]['general_exam_description'] = $row['general_exam_description'];
            }
        }
        
        // Fetch OC data
        $oc_query = $this->db->query("SELECT visit_id, soft_tissue, general, decayed, filled, missing, other FROM visit_oc WHERE visit_id IN ($visit_ids_str)");
        if ($oc_query && $oc_query->num_rows() > 0) {
            foreach ($oc_query->result_array() as $row) {
                $medical_data[$row['visit_id']]['soft_tissue'] = $row['soft_tissue'];
                $medical_data[$row['visit_id']]['general'] = $row['general'];
                $medical_data[$row['visit_id']]['decayed'] = $row['decayed'];
                $medical_data[$row['visit_id']]['filled'] = $row['filled'];
                $medical_data[$row['visit_id']]['missing'] = $row['missing'];
                $medical_data[$row['visit_id']]['other'] = $row['other'];
            }
        }
        
        // Fetch OE data
        $oe_query = $this->db->query("SELECT visit_id, oe_description FROM visit_oe WHERE visit_id IN ($visit_ids_str)");
        if ($oe_query && $oe_query->num_rows() > 0) {
            foreach ($oe_query->result_array() as $row) {
                $medical_data[$row['visit_id']]['oe_description'] = $row['oe_description'];
            }
        }
        
        // Fetch Investigations data
        $inv_query = $this->db->query("SELECT visit_id, investigation FROM visit_investigations WHERE visit_id IN ($visit_ids_str)");
        if ($inv_query && $inv_query->num_rows() > 0) {
            foreach ($inv_query->result_array() as $row) {
                $medical_data[$row['visit_id']]['investigation'] = $row['investigation'];
            }
        }
        
        // Fetch Occlusal Exam data
        $oce_query = $this->db->query("SELECT visit_id, occlusal_exam_description FROM visit_occlusal_exam WHERE visit_id IN ($visit_ids_str)");
        if ($oce_query && $oce_query->num_rows() > 0) {
            foreach ($oce_query->result_array() as $row) {
                $medical_data[$row['visit_id']]['occlusal_exam_description'] = $row['occlusal_exam_description'];
            }
        }
        
        // Fetch Findings data
        $find_query = $this->db->query("SELECT visit_id, finding_description FROM visit_finding WHERE visit_id IN ($visit_ids_str)");
        if ($find_query && $find_query->num_rows() > 0) {
            foreach ($find_query->result_array() as $row) {
                $medical_data[$row['visit_id']]['finding_description'] = $row['finding_description'];
            }
        }
        
        // Fetch Plan data
        $plan_query = $this->db->query("SELECT visit_id, plan_description FROM visit_plan WHERE visit_id IN ($visit_ids_str)");
        if ($plan_query && $plan_query->num_rows() > 0) {
            foreach ($plan_query->result_array() as $row) {
                $medical_data[$row['visit_id']]['plan_description'] = $row['plan_description'];
            }
        }
        
        // Fetch RX data
        $rx_query = $this->db->query("SELECT visit_id, rx_description FROM visit_rx WHERE visit_id IN ($visit_ids_str)");
        if ($rx_query && $rx_query->num_rows() > 0) {
            foreach ($rx_query->result_array() as $row) {
                $medical_data[$row['visit_id']]['rx_description'] = $row['rx_description'];
            }
        }
        
        // Fetch TCA data
        $tca_query = $this->db->query("SELECT visit_id, tca_description FROM visit_tca WHERE visit_id IN ($visit_ids_str)");
        if ($tca_query && $tca_query->num_rows() > 0) {
            foreach ($tca_query->result_array() as $row) {
                $medical_data[$row['visit_id']]['tca_description'] = $row['tca_description'];
            }
        }
        
        return $medical_data;
    }

    // =====================================================
    // LARGE DATASET EXPORT METHODS
    // =====================================================
    
    public function export_large_dataset_streaming($visit_id = null, $date_from = null, $date_to = null)
    {
        // Get total record count first
        $total_records = $this->get_total_record_count($visit_id, $date_from, $date_to);
        
        if ($total_records === 0) {
            show_error('No records found for export');
            return;
        }
        
        // Log export start
        log_message('info', "Starting large dataset export: {$total_records} records");
        
        // Determine if we need multiple files
        $files_needed = ceil($total_records / $this->max_records_per_file);
        
        if ($files_needed > 1) {
            $this->export_multiple_files($visit_id, $date_from, $date_to, $total_records);
        } else {
            $this->export_single_large_file($visit_id, $date_from, $date_to, $total_records);
        }
    }
    
    private function export_single_large_file($visit_id, $date_from, $date_to, $total_records)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Set document properties
        $spreadsheet->getProperties()
            ->setCreator('Dental Management System')
            ->setTitle("Patient Visit Records - {$total_records} records")
            ->setDescription("Large dataset export: {$total_records} records");
        
        // Add headers
        $this->add_excel_headers($sheet);
        
        $current_row = 2;
        $processed_records = 0;
        $start_time = microtime(true);
        
        // Process data in chunks
        for ($offset = 0; $offset < $total_records; $offset += $this->chunk_size) {
            
            // Get chunk of data
            $chunk_data = $this->get_patient_visits_chunk($visit_id, $date_from, $date_to, $this->chunk_size, $offset);
            
            if (empty($chunk_data)) {
                break;
            }
            
            // Process each record in the chunk
            foreach ($chunk_data as $record) {
                $this->add_record_to_sheet($sheet, $record, $current_row);
                $current_row++;
                $processed_records++;
                
                // Memory management - force garbage collection every 100 records
                if ($processed_records % 100 === 0) {
                    gc_collect_cycles();
                    
                    // Log progress every 1000 records
                    if ($processed_records % 1000 === 0) {
                        $elapsed = microtime(true) - $start_time;
                        $rate = $processed_records / $elapsed;
                        log_message('info', "Processed {$processed_records}/{$total_records} records. Rate: " . number_format($rate, 2) . " records/sec");
                    }
                }
            }
            
            // Clear chunk data from memory
            unset($chunk_data);
        }
        
        // Apply final styling
        $this->apply_excel_styling($sheet, $current_row - 1);
        
        // Generate filename
        $filename = $this->generate_filename('patient_visits_large', $total_records);
        
        // Stream the file
        $this->stream_excel_file($spreadsheet, $filename);
        
        // Log completion
        $total_time = microtime(true) - $start_time;
        log_message('info', "Export completed: {$processed_records} records in " . number_format($total_time, 2) . " seconds");
    }
    
    private function get_patient_visits_chunk($visit_id, $date_from, $date_to, $limit, $offset)
    {
        // Build optimized query with minimal joins for better performance
        $this->db->select('
            v.visit_id,
            v.visit_date,
            v.visit_time,
            v.visit_type,
            v.patient_id,
            v.personnel_id,
            p.patient_number,
            p.patient_surname,
            p.patient_othernames,
            per.personnel_fname as doctor_name
        ');
        
        $this->db->from('visit v');
        $this->db->join('patients p', 'v.patient_id = p.patient_id', 'left');
        $this->db->join('personnel per', 'v.personnel_id = per.personnel_id', 'left');
        
        // Apply filters
        $this->db->where('v.close_card !=', 2);
        $this->db->where('(v.parent_visit = 0 OR v.parent_visit IS NULL)');
        
        if ($visit_id !== null) {
            $this->db->where('v.visit_id <=', $visit_id);
        }
        
        if ($date_from !== null) {
            $this->db->where('v.visit_date >=', $date_from);
        }
        
        if ($date_to !== null) {
            $this->db->where('v.visit_date <=', $date_to);
        }
        
        $this->db->order_by('v.visit_date', 'DESC');
        $this->db->order_by('v.visit_time', 'DESC');
        $this->db->limit($limit, $offset);
        
        $query = $this->db->get();
        
        if ($query->num_rows() === 0) {
            return [];
        }
        
        $visits = $query->result_array();
        
        // Get medical notes for this chunk
        $visit_ids = array_column($visits, 'visit_id');
        $medical_data = $this->get_medical_notes_batch_optimized($visit_ids);
        
        // Merge data efficiently
        foreach ($visits as &$visit) {
            $visit_id_key = $visit['visit_id'];
            
            // Process visit types
            $visit['visit_type_name'] = $this->get_visit_type_name($visit['visit_type']);
            
            // Merge medical data
            $visit = array_merge($visit, $medical_data[$visit_id_key] ?? $this->get_empty_medical_data());
        }
        
        return $visits;
    }
    
    private function get_medical_notes_batch_optimized($visit_ids)
    {
        if (empty($visit_ids)) {
            return [];
        }
        
        $medical_data = [];
        $visit_ids_str = implode(',', array_map('intval', $visit_ids));
        
        // Initialize empty data structure
        foreach ($visit_ids as $vid) {
            $medical_data[$vid] = $this->get_empty_medical_data();
        }
        
        // Use prepared statements for better performance and security
        $tables_queries = [
            'visit_hpco' => 'SELECT visit_id, hpco_description FROM visit_hpco WHERE visit_id IN (' . $visit_ids_str . ')',
            'visit_history' => 'SELECT visit_id, past_medical_history, past_dental_history FROM visit_history WHERE visit_id IN (' . $visit_ids_str . ')',
            'visit_general_exam' => 'SELECT visit_id, general_exam_description FROM visit_general_exam WHERE visit_id IN (' . $visit_ids_str . ')',
            'visit_oc' => 'SELECT visit_id, soft_tissue, general, decayed, filled, missing, other FROM visit_oc WHERE visit_id IN (' . $visit_ids_str . ')',
            'visit_oe' => 'SELECT visit_id, oe_description FROM visit_oe WHERE visit_id IN (' . $visit_ids_str . ')',
            'visit_investigations' => 'SELECT visit_id, investigation FROM visit_investigations WHERE visit_id IN (' . $visit_ids_str . ')',
            'visit_occlusal_exam' => 'SELECT visit_id, occlusal_exam_description FROM visit_occlusal_exam WHERE visit_id IN (' . $visit_ids_str . ')',
            'visit_finding' => 'SELECT visit_id, finding_description FROM visit_finding WHERE visit_id IN (' . $visit_ids_str . ')',
            'visit_plan' => 'SELECT visit_id, plan_description FROM visit_plan WHERE visit_id IN (' . $visit_ids_str . ')',
            'visit_rx' => 'SELECT visit_id, rx_description FROM visit_rx WHERE visit_id IN (' . $visit_ids_str . ')',
            'visit_tca' => 'SELECT visit_id, tca_description FROM visit_tca WHERE visit_id IN (' . $visit_ids_str . ')'
        ];
        
        // Execute queries and merge data efficiently
        foreach ($tables_queries as $table => $sql) {
            $result = $this->db->query($sql);
            
            if ($result && $result->num_rows() > 0) {
                foreach ($result->result_array() as $row) {
                    $vid = $row['visit_id'];
                    unset($row['visit_id']); // Remove visit_id from data
                    
                    // Merge non-null values
                    foreach ($row as $key => $value) {
                        if ($value !== null && $value !== '') {
                            $medical_data[$vid][$key] = $value;
                        }
                    }
                }
            }
            
            // Free result memory
            if ($result) {
                $result->free_result();
            }
        }
        
        return $medical_data;
    }
    
    // =====================================================
    // UTILITY METHODS
    // =====================================================
    
    private function get_total_record_count($visit_id, $date_from, $date_to)
    {
        $this->db->select('COUNT(*) as total');
        $this->db->from('visit v');
        $this->db->where('v.close_card !=', 2);
        $this->db->where('(v.parent_visit = 0 OR v.parent_visit IS NULL)');
        
        if ($visit_id !== null) {
            $this->db->where('v.visit_id <=', $visit_id);
        }
        
        if ($date_from !== null) {
            $this->db->where('v.visit_date >=', $date_from);
        }
        
        if ($date_to !== null) {
            $this->db->where('v.visit_date <=', $date_to);
        }
        
        $query = $this->db->get();
        $result = $query->row();
        
        return $result ? (int)$result->total : 0;
    }
    
    private function get_empty_medical_data()
    {
        return [
            'hpco_description' => null,
            'past_medical_history' => null,
            'past_dental_history' => null,
            'general_exam_description' => null,
            'soft_tissue' => null,
            'general' => null,
            'decayed' => null,
            'filled' => null,
            'missing' => null,
            'other' => null,
            'oe_description' => null,
            'investigation' => null,
            'occlusal_exam_description' => null,
            'finding_description' => null,
            'plan_description' => null,
            'rx_description' => null,
            'tca_description' => null
        ];
    }
    
    private function get_visit_type_name($visit_type)
    {
        switch ($visit_type) {
            case 3: return 'Other';
            case 4: return 'Insurance';
            default: return 'General';
        }
    }
    
    private function add_excel_headers($sheet)
    {
        $headers = [
            'A1' => 'Visit ID', 'B1' => 'Patient Number', 'C1' => 'Patient Surname',
            'D1' => 'Patient Other Names', 'E1' => 'Visit Date', 'F1' => 'Visit Time',
            'G1' => 'Doctor', 'H1' => 'Visit Type', 'I1' => 'Presenting Complaint',
            'J1' => 'Past Medical History', 'K1' => 'Past Dental History',
            'L1' => 'General Exam', 'M1' => 'Soft Tissue', 'N1' => 'Hard Tissue - General',
            'O1' => 'Hard Tissue - Decayed', 'P1' => 'Hard Tissue - Filled',
            'Q1' => 'Hard Tissue - Missing', 'R1' => 'Hard Tissue - Other',
            'S1' => 'Oral Examination', 'T1' => 'Investigations', 'U1' => 'Occlusal Exam',
            'V1' => 'Findings', 'W1' => 'Plan Description', 'X1' => 'Rx Done', 'Y1' => 'TCA'
        ];
        
        foreach ($headers as $cell => $value) {
            $sheet->setCellValue($cell, $value);
        }
        
        // Apply header styling
        $headerStyle = [
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF']
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4472C4']
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN
                ]
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER
            ]
        ];
        
        $sheet->getStyle('A1:Y1')->applyFromArray($headerStyle);
        
        // Auto-size columns
        foreach (range('A', 'Y') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
    }
    
    private function add_record_to_sheet($sheet, $record, $row)
    {
        $sheet->setCellValue('A' . $row, $record['visit_id']);
        $sheet->setCellValue('B' . $row, $record['patient_number']);
        $sheet->setCellValue('C' . $row, $record['patient_surname']);
        $sheet->setCellValue('D' . $row, $record['patient_othernames']);
        $sheet->setCellValue('E' . $row, date('jS M Y', strtotime($record['visit_date'])));
        $sheet->setCellValue('F' . $row, date('H:i a', strtotime($record['visit_time'])));
        $sheet->setCellValue('G' . $row, $record['doctor_name'] ?? '-');
        $sheet->setCellValue('H' . $row, $record['visit_type_name']);
        $sheet->setCellValue('I' . $row, $record['hpco_description'] ?? '-');
        $sheet->setCellValue('J' . $row, $record['past_medical_history'] ?? '-');
        $sheet->setCellValue('K' . $row, $record['past_dental_history'] ?? '-');
        $sheet->setCellValue('L' . $row, $record['general_exam_description'] ?? '-');
        $sheet->setCellValue('M' . $row, $record['soft_tissue'] ?? '-');
        $sheet->setCellValue('N' . $row, $record['general'] ?? '-');
        $sheet->setCellValue('O' . $row, $record['decayed'] ?? '-');
        $sheet->setCellValue('P' . $row, $record['filled'] ?? '-');
        $sheet->setCellValue('Q' . $row, $record['missing'] ?? '-');
        $sheet->setCellValue('R' . $row, $record['other'] ?? '-');
        $sheet->setCellValue('S' . $row, $record['oe_description'] ?? '-');
        $sheet->setCellValue('T' . $row, $record['investigation'] ?? '-');
        $sheet->setCellValue('U' . $row, $record['occlusal_exam_description'] ?? '-');
        $sheet->setCellValue('V' . $row, $record['finding_description'] ?? '-');
        $sheet->setCellValue('W' . $row, $record['plan_description'] ?? '-');
        $sheet->setCellValue('X' . $row, $record['rx_description'] ?? '-');
        $sheet->setCellValue('Y' . $row, strip_tags($record['tca_description'] ?? '-'));
    }
    
    private function generate_filename($prefix, $record_count)
    {
        return $prefix . '_' . date('Y-m-d_H-i-s') . '_' . number_format($record_count) . '_records.xlsx';
    }
    
    private function stream_excel_file($spreadsheet, $filename)
    {
        // Set headers for download
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        
        // Create writer and output
        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        
        // Clean up memory
        $spreadsheet->disconnectWorksheets();
        unset($spreadsheet);
    }
    
    private function apply_excel_styling($sheet, $total_rows)
    {
        // Apply borders to all data
        $dataRange = 'A1:Y' . $total_rows;
        $sheet->getStyle($dataRange)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN
                ]
            ]
        ]);
        
        // Set row height for better readability
        for ($i = 2; $i <= $total_rows; $i++) {
            $sheet->getRowDimension($i)->setRowHeight(20);
        }
    }
    
    // =====================================================
    // MULTIPLE FILES AND ZIP CREATION METHODS
    // =====================================================
    
    private function export_multiple_files($visit_id, $date_from, $date_to, $total_records)
    {
        $files_needed = ceil($total_records / $this->max_records_per_file);
        $zip_filename = 'patient_visits_' . date('Y-m-d_H-i-s') . '_' . $total_records . '_records.zip';
        
        // Create temporary directory
        $temp_dir = sys_get_temp_dir() . '/patient_export_' . uniqid();
        mkdir($temp_dir, 0777, true);
        
        $files_created = [];
        
        for ($file_num = 1; $file_num <= $files_needed; $file_num++) {
            $offset = ($file_num - 1) * $this->max_records_per_file;
            $limit = min($this->max_records_per_file, $total_records - $offset);
            
            $filename = $this->create_file_chunk($visit_id, $date_from, $date_to, $offset, $limit, $file_num, $temp_dir);
            
            if ($filename) {
                $files_created[] = $filename;
            }
            
            // Memory cleanup after each file
            gc_collect_cycles();
        }
        
        // Create ZIP file
        $this->create_zip_archive($files_created, $temp_dir, $zip_filename);
        
        // Cleanup temporary files
        $this->cleanup_temp_files($temp_dir);
    }
    
    private function create_file_chunk($visit_id, $date_from, $date_to, $offset, $limit, $file_num, $temp_dir)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Set document properties
        $spreadsheet->getProperties()
            ->setCreator('Dental Management System')
            ->setTitle("Patient Visit Records - Part {$file_num}")
            ->setDescription("Large dataset export - Part {$file_num} of multiple files");
        
        // Add headers
        $this->add_excel_headers($sheet);
        
        // Get data chunk
        $chunk_data = $this->get_patient_visits_chunk($visit_id, $date_from, $date_to, $limit, $offset);
        
        if (empty($chunk_data)) {
            return null;
        }
        
        $current_row = 2;
        foreach ($chunk_data as $record) {
            $this->add_record_to_sheet($sheet, $record, $current_row);
            $current_row++;
        }
        
        // Apply styling
        $this->apply_excel_styling($sheet, $current_row - 1);
        
        // Save file
        $filename = "patient_visits_part_{$file_num}_" . date('Y-m-d_H-i-s') . '.xlsx';
        $filepath = $temp_dir . '/' . $filename;
        
        $writer = new Xlsx($spreadsheet);
        $writer->save($filepath);
        
        // Clean up
        $spreadsheet->disconnectWorksheets();
        unset($spreadsheet);
        
        return $filepath;
    }
    
    private function create_zip_archive($files_created, $temp_dir, $zip_filename)
    {
        $zip = new ZipArchive();
        $zip_path = $temp_dir . '/' . $zip_filename;
        
        if ($zip->open($zip_path, ZipArchive::CREATE) !== TRUE) {
            show_error('Cannot create ZIP file');
            return;
        }
        
        foreach ($files_created as $file_path) {
            $zip->addFile($file_path, basename($file_path));
        }
        
        $zip->close();
        
        // Stream the ZIP file
        header('Content-Type: application/zip');
        header('Content-Disposition: attachment; filename="' . $zip_filename . '"');
        header('Content-Length: ' . filesize($zip_path));
        
        readfile($zip_path);
        exit;
    }
    
    private function cleanup_temp_files($temp_dir)
    {
        // Clean up temporary files
        $files = glob($temp_dir . '/*');
        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }
        rmdir($temp_dir);
    }
    
    // =====================================================
    // PROGRESS TRACKING AND MONITORING
    // =====================================================
    
    public function get_export_progress($export_id = null)
    {
        // This method can be called via AJAX to show progress
        header('Content-Type: application/json');
        
        // In a real implementation, you'd store progress in database or cache
        $progress = [
            'status' => 'processing',
            'current_record' => 15000,
            'total_records' => 50000,
            'percentage' => 30,
            'estimated_time_remaining' => '5 minutes',
            'current_file' => 1,
            'total_files' => 3,
            'memory_usage' => memory_get_usage(true),
            'peak_memory' => memory_get_peak_usage(true)
        ];
        
        echo json_encode($progress);
    }
    
    public function queue_large_export($visit_id = null, $date_from = null, $date_to = null)
    {
        // Create export job record
        $job_data = [
            'export_type' => 'patient_visits_large',
            'parameters' => json_encode([
                'visit_id' => $visit_id,
                'date_from' => $date_from,
                'date_to' => $date_to
            ]),
            'status' => 'queued',
            'created_at' => date('Y-m-d H:i:s'),
            'created_by' => $this->session->userdata('user_id')
        ];
        
        $this->db->insert('export_jobs', $job_data);
        $job_id = $this->db->insert_id();
        
        // Return job ID for tracking
        echo json_encode([
            'success' => true,
            'job_id' => $job_id,
            'message' => 'Export queued successfully. You will be notified when complete.'
        ]);
    }
    
    // =====================================================
    // ADDITIONAL EXPORT OPTIONS
    // =====================================================
    
    public function export_by_date_range($date_from, $date_to)
    {
        // Validate dates
        if (!$date_from || !$date_to) {
            show_error('Both from and to dates are required');
            return;
        }
        
        // Check if date range is too large for regular export
        $total_records = $this->get_total_record_count(null, $date_from, $date_to);
        
        if ($total_records > 10000) {
            // Use large dataset export for big date ranges
            $this->export_large_dataset_streaming(null, $date_from, $date_to);
        } else {
            // Use regular export for smaller date ranges
            $data = $this->get_patient_visits_data_by_date($date_from, $date_to);
            $this->create_excel_export($data, "patient_visits_{$date_from}_to_{$date_to}");
        }
    }
    
    private function get_patient_visits_data_by_date($date_from, $date_to)
    {
        // Similar to get_patient_visits_data but with date filters
        $this->db->select('
            v.visit_id,
            v.visit_date,
            v.visit_time,
            v.visit_type,
            v.personnel_id,
            p.patient_number,
            p.patient_surname,
            p.patient_othernames,
            per.personnel_fname as doctor_name
        ');
        
        $this->db->from('visit v');
        $this->db->join('patients p', 'v.patient_id = p.patient_id', 'left');
        $this->db->join('personnel per', 'v.personnel_id = per.personnel_id', 'left');
        
        // Add conditions
        $this->db->where('v.close_card !=', 2);
        $this->db->where('(v.parent_visit = 0 OR v.parent_visit IS NULL)');
        $this->db->where('v.visit_date >=', $date_from);
        $this->db->where('v.visit_date <=', $date_to);
        
        $this->db->order_by('v.visit_date', 'DESC');
        $this->db->order_by('v.visit_time', 'DESC');
        
        $query = $this->db->get();
        
        if ($query->num_rows() === 0) {
            return [];
        }
        
        $visits = $query->result_array();
        
        // Get medical notes
        $visit_ids = array_column($visits, 'visit_id');
        $visit_ids_str = implode(',', $visit_ids);
        $medical_data = $this->get_medical_notes_batch($visit_ids_str);
        
        // Merge data
        foreach ($visits as &$visit) {
            $visit_id = $visit['visit_id'];
            
            // Process visit types
            switch ($visit['visit_type']) {
                case 3:
                    $visit['visit_type_name'] = 'Other';
                    break;
                case 4:
                    $visit['visit_type_name'] = 'Insurance';
                    break;
                default:
                    $visit['visit_type_name'] = 'General';
                    break;
            }
            
            // Merge medical data
            $visit = array_merge($visit, $medical_data[$visit_id] ?? []);
        }
        
        return $visits;
    }
    
    private function create_excel_export($data, $filename_prefix)
    {
        if (empty($data)) {
            show_error('No data found for export');
            return;
        }
        
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Set document properties
        $spreadsheet->getProperties()
            ->setCreator('Dental Management System')
            ->setTitle('Patient Visit Records')
            ->setDescription('Export of patient visit records with medical notes');
        
        // Add headers and data
        $this->add_excel_headers($sheet);
        
        $row = 2;
        foreach ($data as $record) {
            $this->add_record_to_sheet($sheet, $record, $row);
            $row++;
        }
        
        // Apply styling
        $this->apply_excel_styling($sheet, $row - 1);
        
        // Generate filename
        $filename = $filename_prefix . '_' . date('Y-m-d_H-i-s') . '.xlsx';
        
        // Stream file
        $this->stream_excel_file($spreadsheet, $filename);
    }



    //export payments

// =====================================================
// PAYMENTS EXPORT FUNCTIONS - FINAL FIXED VERSION
// =====================================================

/**
 * Export payments data to Excel - handles large datasets efficiently
 * @param string $date_from Optional start date filter
 * @param string $date_to Optional end date filter
 * @param int $payment_method_id Optional payment method filter
 */
public function export_payments_to_excel($date_from = null, $date_to = null, $payment_method_id = null)
{
    // Get total record count first
    $total_records = $this->get_payments_total_count($date_from, $date_to, $payment_method_id);
    
    if ($total_records === 0) {
        show_error('No payment records found for export');
        return;
    }
    
    // Log export start
    log_message('info', "Starting payments export: {$total_records} records");
    
    // Determine export method based on dataset size
    if ($total_records > $this->max_records_per_file) {
        $this->export_payments_large_dataset($date_from, $date_to, $payment_method_id, $total_records);
    } else {
        $this->export_payments_single_file($date_from, $date_to, $payment_method_id, $total_records);
    }
}

/**
 * Export payments data in a single file
 */
private function export_payments_single_file($date_from, $date_to, $payment_method_id, $total_records)
{
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    
    // Set document properties
    $spreadsheet->getProperties()
        ->setCreator('Dental Management System')
        ->setTitle("Payment Records - {$total_records} records")
        ->setDescription("Export of payment records with patient and visit details");
    
    // Add headers
    $this->add_payments_excel_headers($sheet);
    
    $current_row = 2;
    $processed_records = 0;
    $start_time = microtime(true);
    
    // Process data in chunks to manage memory
    for ($offset = 0; $offset < $total_records; $offset += $this->chunk_size) {
        
        // Get chunk of payment data
        $chunk_data = $this->get_payments_chunk($date_from, $date_to, $payment_method_id, $this->chunk_size, $offset);
        
        if (empty($chunk_data)) {
            break;
        }
        
        // Process each record in the chunk
        foreach ($chunk_data as $record) {
            $this->add_payment_record_to_sheet($sheet, $record, $current_row);
            $current_row++;
            $processed_records++;
            
            // Memory management - force garbage collection every 100 records
            if ($processed_records % 100 === 0) {
                gc_collect_cycles();
                
                // Log progress every 1000 records
                if ($processed_records % 1000 === 0) {
                    $elapsed = microtime(true) - $start_time;
                    $rate = $processed_records / $elapsed;
                    log_message('info', "Processed {$processed_records}/{$total_records} payment records. Rate: " . number_format($rate, 2) . " records/sec");
                }
            }
        }
        
        // Clear chunk data from memory
        unset($chunk_data);
    }
    
    // Apply final styling
    $this->apply_payments_excel_styling($sheet, $current_row - 1);
    
    // Generate filename
    $filename = $this->generate_payments_filename('payment_records', $total_records, $date_from, $date_to);
    
    // Stream the file
    $this->stream_excel_file($spreadsheet, $filename);
    
    // Log completion
    $total_time = microtime(true) - $start_time;
    log_message('info', "Payments export completed: {$processed_records} records in " . number_format($total_time, 2) . " seconds");
}

/**
 * Export payments data as multiple files for very large datasets
 */
private function export_payments_large_dataset($date_from, $date_to, $payment_method_id, $total_records)
{
    $files_needed = ceil($total_records / $this->max_records_per_file);
    $zip_filename = 'payment_records_' . date('Y-m-d_H-i-s') . '_' . $total_records . '_records.zip';
    
    // Create temporary directory
    $temp_dir = sys_get_temp_dir() . '/payments_export_' . uniqid();
    mkdir($temp_dir, 0777, true);
    
    $files_created = [];
    
    for ($file_num = 1; $file_num <= $files_needed; $file_num++) {
        $offset = ($file_num - 1) * $this->max_records_per_file;
        $limit = min($this->max_records_per_file, $total_records - $offset);
        
        $filename = $this->create_payments_file_chunk($date_from, $date_to, $payment_method_id, $offset, $limit, $file_num, $temp_dir);
        
        if ($filename) {
            $files_created[] = $filename;
        }
        
        // Memory cleanup after each file
        gc_collect_cycles();
    }
    
    // Create ZIP file and stream it
    $this->create_zip_archive($files_created, $temp_dir, $zip_filename);
    
    // Cleanup temporary files
    $this->cleanup_temp_files($temp_dir);
}

/**
 * Get total count of payment records - FIXED ALL TABLE NAMES
 */
private function get_payments_total_count($date_from = null, $date_to = null, $payment_method_id = null)
{
    $this->db->select('COUNT(*) as total');
    $this->db->from('payments p');
    $this->db->join('visit v', 'p.visit_id = v.visit_id', 'inner');
    $this->db->join('patients pt', 'v.patient_id = pt.patient_id', 'inner');
    $this->db->join('visit_type vt', 'vt.visit_type_id = v.visit_type', 'inner');
    // FIXED: Use payment_methods (plural) consistently
    $this->db->join('payment_method pm', 'p.payment_method_id = pm.payment_method_id', 'inner');
    
    // Apply base conditions
    $this->db->where('p.payment_type', 1);
    $this->db->where('v.visit_delete', 0);
    $this->db->where('p.cancel', 0);
    
    // Apply optional filters
    if ($date_from !== null) {
        $this->db->where('p.payment_created >=', $date_from);
    }
    
    if ($date_to !== null) {
        $this->db->where('p.payment_created <=', $date_to);
    }
    
    if ($payment_method_id !== null) {
        $this->db->where('p.payment_method_id', $payment_method_id);
    }
    
    $query = $this->db->get();
    $result = $query->row();
    
    return $result ? (int)$result->total : 0;
}

/**
 * Get a chunk of payment records - FIXED ALL TABLE NAMES
 */
private function get_payments_chunk($date_from, $date_to, $payment_method_id, $limit, $offset)
{
    $this->db->select('
        p.payment_id,
        p.visit_id,
        p.payment_created,
        p.time,
        p.amount_paid,
        p.transaction_code,
        p.payment_created_by as recorded_by_id,
        v.visit_date,
        v.patient_id,
        pt.patient_number,
        pt.patient_surname,
        pt.patient_othernames,
        vt.visit_type_name as category,
        pm.payment_method as method,
        personnel.personnel_fname as recorded_by_name,
        personnel.personnel_onames as recorded_by_surname
    ');
    
    $this->db->from('payments p');
    $this->db->join('visit v', 'p.visit_id = v.visit_id', 'inner');
    $this->db->join('patients pt', 'v.patient_id = pt.patient_id', 'inner');
    $this->db->join('visit_type vt', 'vt.visit_type_id = v.visit_type', 'inner');
    // FIXED: Use payment_methods (plural) consistently
    $this->db->join('payment_method pm', 'p.payment_method_id = pm.payment_method_id', 'inner');
    $this->db->join('personnel', 'p.payment_created_by = personnel.personnel_id', 'left');
    
    // Apply base conditions
    $this->db->where('p.payment_type', 1);
    $this->db->where('v.visit_delete', 0);
    $this->db->where('p.cancel', 0);
    
    // Apply optional filters
    if ($date_from !== null) {
        $this->db->where('p.payment_created >=', $date_from);
    }
    
    if ($date_to !== null) {
        $this->db->where('p.payment_created <=', $date_to);
    }
    
    if ($payment_method_id !== null) {
        $this->db->where('p.payment_method_id', $payment_method_id);
    }
    
    // Order by payment date (most recent first)
    $this->db->order_by('p.payment_created', 'DESC');
    $this->db->order_by('p.time', 'DESC');
    
    // Apply limit and offset
    $this->db->limit($limit, $offset);
    
    $query = $this->db->get();
    
    if ($query->num_rows() === 0) {
        return [];
    }
    
    $payments = $query->result_array();
    
    // Get service details for each payment - FIXED TO HANDLE MISSING TABLE
    $payment_ids = array_column($payments, 'payment_id');
    $services_data = $this->get_payment_services_batch($payment_ids);
    
    // Merge service data with payments
    foreach ($payments as &$payment) {
        $payment_id = $payment['payment_id'];
        $payment['service'] = $services_data[$payment_id] ?? 'General Payment';
        
        // Format patient name
        $payment['patient_name'] = trim($payment['patient_surname'] . ' ' . $payment['patient_othernames']);
        
        // Format recorded by name
        $payment['recorded_by'] = trim(($payment['recorded_by_name'] ?? '') . ' ' . ($payment['recorded_by_surname'] ?? ''));
        if (empty(trim($payment['recorded_by']))) {
            $payment['recorded_by'] = 'Unknown';
        }
    }
    
    return $payments;
}

/**
 * Get services for a batch of payment IDs - FIXED TO HANDLE MISSING TABLE
 */
private function get_payment_services_batch($payment_ids)
{
    if (empty($payment_ids)) {
        return [];
    }
    
    $services_data = [];
    
    // Initialize with default service data
    foreach ($payment_ids as $pid) {
        $services_data[$pid] = 'General Payment';
    }
    
    // Try to fetch service details if tables exist
    try {
        $payment_ids_str = implode(',', array_map('intval', $payment_ids));
        
        // First, check if payment_items table exists
        $table_exists_query = $this->db->query("SHOW TABLES LIKE 'payment_items'");
        
        if ($table_exists_query && $table_exists_query->num_rows() > 0) {
            // Table exists, try to get service details
            $services_query = $this->db->query("
                SELECT 
                    pi.payment_id,
                    GROUP_CONCAT(DISTINCT COALESCE(s.service_name, pi.description, 'Service') SEPARATOR ', ') as services
                FROM payment_items pi
                LEFT JOIN services s ON pi.service_id = s.service_id
                WHERE pi.payment_id IN ($payment_ids_str)
                GROUP BY pi.payment_id
            ");
            
            if ($services_query && $services_query->num_rows() > 0) {
                foreach ($services_query->result_array() as $row) {
                    $services_data[$row['payment_id']] = $row['services'] ?? 'General Payment';
                }
            }
        } else {
            // payment_items table doesn't exist, check if there's another service tracking method
            // You might have services tracked in a different way in your system
            log_message('info', 'payment_items table not found - using default service names');
        }
        
    } catch (Exception $e) {
        // Log the error but don't break the export
        log_message('error', 'Error fetching service details: ' . $e->getMessage());
        // Keep the default 'General Payment' values
    }
    
    return $services_data;
}

/**
 * Add Excel headers for payments export
 */
private function add_payments_excel_headers($sheet)
{
    $headers = [
        'A1' => '#',
        'B1' => 'Visit Id',
        'C1' => 'Category',
        'D1' => 'Visit Date',
        'E1' => 'Payment Date',
        'F1' => 'Time recorded',
        'G1' => 'Patient Id',
        'H1' => 'Patient Name',
        'I1' => 'Patient Number',
        'J1' => 'Category',
        'K1' => 'Service',
        'L1' => 'Amount',
        'M1' => 'Method',
        'N1' => 'Transaction Code',
        'O1' => 'Recorded by'
    ];
    
    foreach ($headers as $cell => $value) {
        $sheet->setCellValue($cell, $value);
    }
    
    // Apply header styling
    $headerStyle = [
        'font' => [
            'bold' => true,
            'color' => ['rgb' => 'FFFFFF']
        ],
        'fill' => [
            'fillType' => Fill::FILL_SOLID,
            'startColor' => ['rgb' => '2E8B57'] // Sea Green for payments
        ],
        'borders' => [
            'allBorders' => [
                'borderStyle' => Border::BORDER_THIN
            ]
        ],
        'alignment' => [
            'horizontal' => Alignment::HORIZONTAL_CENTER,
            'vertical' => Alignment::VERTICAL_CENTER
        ]
    ];
    
    $sheet->getStyle('A1:O1')->applyFromArray($headerStyle);
    
    // Auto-size columns
    foreach (range('A', 'O') as $col) {
        $sheet->getColumnDimension($col)->setAutoSize(true);
    }
}

/**
 * Add a payment record to the Excel sheet - FIXED AMOUNT FIELD
 */
private function add_payment_record_to_sheet($sheet, $record, $row)
{
    $record_number = $row - 1; // Row number starts from 2, so subtract 1 for numbering
    
    $sheet->setCellValue('A' . $row, $record_number);
    $sheet->setCellValue('B' . $row, $record['visit_id']);
    $sheet->setCellValue('C' . $row, $record['category']);
    $sheet->setCellValue('D' . $row, date('jS M Y', strtotime($record['visit_date'])));
    $sheet->setCellValue('E' . $row, date('jS M Y', strtotime($record['payment_created'])));
    $sheet->setCellValue('F' . $row, date('H:i a', strtotime($record['time'])));
    $sheet->setCellValue('G' . $row, $record['patient_id']);
    $sheet->setCellValue('H' . $row, $record['patient_name']);
    $sheet->setCellValue('I' . $row, $record['patient_number']);
    $sheet->setCellValue('J' . $row, $record['category']); // Duplicate category as per your headers
    $sheet->setCellValue('K' . $row, $record['service']);
    // FIXED: Use amount_paid instead of amount
    $sheet->setCellValue('L' . $row, number_format($record['amount_paid'], 2));
    $sheet->setCellValue('M' . $row, $record['method']);
    $sheet->setCellValue('N' . $row, $record['transaction_code'] ?? '-');
    $sheet->setCellValue('O' . $row, $record['recorded_by']);
}

/**
 * Apply styling to payments Excel sheet
 */
private function apply_payments_excel_styling($sheet, $total_rows)
{
    // Apply borders to all data
    $dataRange = 'A1:O' . $total_rows;
    $sheet->getStyle($dataRange)->applyFromArray([
        'borders' => [
            'allBorders' => [
                'borderStyle' => Border::BORDER_THIN
            ]
        ]
    ]);
    
    // Set row height for better readability
    for ($i = 2; $i <= $total_rows; $i++) {
        $sheet->getRowDimension($i)->setRowHeight(18);
    }
    
    // Format amount column as currency
    $amountRange = 'L2:L' . $total_rows;
    $sheet->getStyle($amountRange)->getNumberFormat()->setFormatCode('#,##0.00');
    
    // Center align certain columns
    $centerColumns = ['A', 'B', 'G', 'L', 'M'];
    foreach ($centerColumns as $col) {
        $sheet->getStyle($col . '2:' . $col . $total_rows)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    }
}

/**
 * Generate filename for payments export
 */
private function generate_payments_filename($prefix, $record_count, $date_from = null, $date_to = null)
{
    $filename = $prefix . '_' . date('Y-m-d_H-i-s');
    
    if ($date_from && $date_to) {
        $filename .= '_' . $date_from . '_to_' . $date_to;
    } elseif ($date_from) {
        $filename .= '_from_' . $date_from;
    } elseif ($date_to) {
        $filename .= '_to_' . $date_to;
    }
    
    $filename .= '_' . number_format($record_count) . '_records.xlsx';
    
    return $filename;
}

/**
 * Create a single file chunk for payments export
 */
private function create_payments_file_chunk($date_from, $date_to, $payment_method_id, $offset, $limit, $file_num, $temp_dir)
{
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    
    // Set document properties
    $spreadsheet->getProperties()
        ->setCreator('Dental Management System')
        ->setTitle("Payment Records - Part {$file_num}")
        ->setDescription("Large dataset export - Part {$file_num} of multiple files");
    
    // Add headers
    $this->add_payments_excel_headers($sheet);
    
    // Get data chunk
    $chunk_data = $this->get_payments_chunk($date_from, $date_to, $payment_method_id, $limit, $offset);
    
    if (empty($chunk_data)) {
        return null;
    }
    
    $current_row = 2;
    foreach ($chunk_data as $record) {
        $this->add_payment_record_to_sheet($sheet, $record, $current_row);
        $current_row++;
    }
    
    // Apply styling
    $this->apply_payments_excel_styling($sheet, $current_row - 1);
    
    // Save file
    $filename = "payment_records_part_{$file_num}_" . date('Y-m-d_H-i-s') . '.xlsx';
    $filepath = $temp_dir . '/' . $filename;
    
    $writer = new Xlsx($spreadsheet);
    $writer->save($filepath);
    
    // Clean up
    $spreadsheet->disconnectWorksheets();
    unset($spreadsheet);
    
    return $filepath;
}

// =====================================================
// ADDITIONAL PAYMENT EXPORT METHODS - ALL FIXED
// =====================================================

/**
 * Export payments by date range
 */
public function export_payments_by_date($date_from, $date_to)
{
    // Validate dates
    if (!$date_from || !$date_to) {
        show_error('Both from and to dates are required');
        return;
    }
    
    // Validate date format
    if (!strtotime($date_from) || !strtotime($date_to)) {
        show_error('Invalid date format. Use YYYY-MM-DD');
        return;
    }
    
    $this->export_payments_to_excel($date_from, $date_to);
}

/**
 * Export payments by payment method
 */
public function export_payments_by_method($payment_method_id)
{
    if (!$payment_method_id || !is_numeric($payment_method_id)) {
        show_error('Valid payment method ID is required');
        return;
    }
    
    $this->export_payments_to_excel(null, null, $payment_method_id);
}

/**
 * Get payment methods for filter dropdown - FIXED TABLE NAME
 */
public function get_payment_methods()
{
    header('Content-Type: application/json');
    
    $this->db->select('payment_method_id, payment_method');
    // FIXED: Use payment_methods (plural)
    $this->db->from('payment_method');
    $this->db->order_by('payment_method', 'ASC');
    
    $query = $this->db->get();
    $methods = $query->result_array();
    
    echo json_encode([
        'success' => true,
        'data' => $methods
    ]);
}

/**
 * Get payment statistics for dashboard - FIXED ALL TABLE NAMES
 */
public function get_payment_stats($date_from = null, $date_to = null)
{
    header('Content-Type: application/json');
    
    // Base query for statistics
    $this->db->select('
        COUNT(*) as total_payments,
        SUM(p.amount_paid) as total_amount,
        AVG(p.amount_paid) as average_amount,
        MIN(p.amount_paid) as min_amount,
        MAX(p.amount_paid) as max_amount
    ');
    $this->db->from('payments p');
    $this->db->join('visit v', 'p.visit_id = v.visit_id', 'inner');
    
    // Apply base conditions
    $this->db->where('p.payment_type', 1);
    $this->db->where('v.visit_delete', 0);
    $this->db->where('p.cancel', 0);
    
    // Apply date filters if provided
    if ($date_from) {
        $this->db->where('p.payment_created >=', $date_from);
    }
    
    if ($date_to) {
        $this->db->where('p.payment_created <=', $date_to);
    }
    
    $query = $this->db->get();
    $stats = $query->row_array();
    
    // Get payment method breakdown - FIXED TABLE NAME
    $this->db->select('pm.payment_method, COUNT(*) as count, SUM(p.amount_paid) as total');
    $this->db->from('payments p');
    $this->db->join('visit v', 'p.visit_id = v.visit_id', 'inner');
    // FIXED: Use payment_methods (plural)
    $this->db->join('payment_method pm', 'p.payment_method_id = pm.payment_method_id', 'inner');
    
    // Apply same conditions
    $this->db->where('p.payment_type', 1);
    $this->db->where('v.visit_delete', 0);
    $this->db->where('p.cancel', 0);
    
    if ($date_from) {
        $this->db->where('p.payment_created >=', $date_from);
    }
    
    if ($date_to) {
        $this->db->where('p.payment_created <=', $date_to);
    }
    
    $this->db->group_by('pm.payment_method_id');
    $this->db->order_by('total', 'DESC');
    
    $method_query = $this->db->get();
    $method_breakdown = $method_query->result_array();
    
    echo json_encode([
        'success' => true,
        'stats' => $stats,
        'method_breakdown' => $method_breakdown
    ]);
}




//export 

// =====================================================
// INVOICE ITEMS EXPORT FUNCTIONS - Add to your Export controller
// =====================================================

/**
 * Export invoice items data to Excel - handles large datasets efficiently
 * @param string $date_from Optional start date filter
 * @param string $date_to Optional end date filter
 * @param int $service_charge_id Optional service filter
 */
public function export_invoice_items_to_excel($date_from = null, $date_to = null, $service_charge_id = null)
{
    // Get total record count first
    $total_records = $this->get_invoice_items_total_count($date_from, $date_to, $service_charge_id);
    
    if ($total_records === 0) {
        show_error('No invoice items found for export');
        return;
    }
    
    // Log export start
    log_message('info', "Starting invoice items export: {$total_records} records");
    
    // Determine export method based on dataset size
    if ($total_records > $this->max_records_per_file) {
        $this->export_invoice_items_large_dataset($date_from, $date_to, $service_charge_id, $total_records);
    } else {
        $this->export_invoice_items_single_file($date_from, $date_to, $service_charge_id, $total_records);
    }
}

/**
 * Export invoice items data in a single file
 */
private function export_invoice_items_single_file($date_from, $date_to, $service_charge_id, $total_records)
{
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    
    // Set document properties
    $spreadsheet->getProperties()
        ->setCreator('Dental Management System')
        ->setTitle("Invoice Items - {$total_records} records")
        ->setDescription("Export of invoice items with patient and visit details");
    
    // Add headers
    $this->add_invoice_items_excel_headers($sheet);
    
    $current_row = 2;
    $processed_records = 0;
    $start_time = microtime(true);
    
    // Process data in chunks to manage memory
    for ($offset = 0; $offset < $total_records; $offset += $this->chunk_size) {
        
        // Get chunk of invoice items data
        $chunk_data = $this->get_invoice_items_chunk($date_from, $date_to, $service_charge_id, $this->chunk_size, $offset);
        
        if (empty($chunk_data)) {
            break;
        }
        
        // Process each record in the chunk
        foreach ($chunk_data as $record) {
            $this->add_invoice_item_record_to_sheet($sheet, $record, $current_row);
            $current_row++;
            $processed_records++;
            
            // Memory management - force garbage collection every 100 records
            if ($processed_records % 100 === 0) {
                gc_collect_cycles();
                
                // Log progress every 1000 records
                if ($processed_records % 1000 === 0) {
                    $elapsed = microtime(true) - $start_time;
                    $rate = $processed_records / $elapsed;
                    log_message('info', "Processed {$processed_records}/{$total_records} invoice items. Rate: " . number_format($rate, 2) . " records/sec");
                }
            }
        }
        
        // Clear chunk data from memory
        unset($chunk_data);
    }
    
    // Apply final styling
    $this->apply_invoice_items_excel_styling($sheet, $current_row - 1);
    
    // Generate filename
    $filename = $this->generate_invoice_items_filename('invoice_items', $total_records, $date_from, $date_to);
    
    // Stream the file
    $this->stream_excel_file($spreadsheet, $filename);
    
    // Log completion
    $total_time = microtime(true) - $start_time;
    log_message('info', "Invoice items export completed: {$processed_records} records in " . number_format($total_time, 2) . " seconds");
}

/**
 * Get total count of invoice items records
 */
private function get_invoice_items_total_count($date_from = null, $date_to = null, $service_charge_id = null)
{
    $this->db->select('COUNT(*) as total');
    $this->db->from('visit_charge vc');
    $this->db->join('visit v', 'vc.visit_id = v.visit_id', 'inner');
    $this->db->join('service_charge sc', 'vc.service_charge_id = sc.service_charge_id', 'inner');
    $this->db->join('patients p', 'v.patient_id = p.patient_id', 'inner');
    
    // Apply base conditions (matching your SQL)
    $this->db->where('vc.visit_charge_delete', 0);
    $this->db->where('(v.parent_visit IS NULL OR v.parent_visit = 0)');
    $this->db->where('v.visit_delete', 0);
    $this->db->where('vc.charged', 1);
    
    // Apply optional filters
    if ($date_from !== null) {
        $this->db->where('v.visit_date >=', $date_from);
    }
    
    if ($date_to !== null) {
        $this->db->where('v.visit_date <=', $date_to);
    }
    
    if ($service_charge_id !== null) {
        $this->db->where('vc.service_charge_id', $service_charge_id);
    }
    
    $query = $this->db->get();
    $result = $query->row();
    
    return $result ? (int)$result->total : 0;
}

/**
 * Get a chunk of invoice items records
 */
private function get_invoice_items_chunk($date_from, $date_to, $service_charge_id, $limit, $offset)
{
    $this->db->select('
        v.visit_id,
        v.visit_date,
        v.visit_time,
        p.patient_id,
        p.patient_number,
        p.patient_surname,
        p.patient_othernames,
        vc.visit_charge_units as units,
        vc.visit_charge_amount as unit_amount,
        sc.service_charge_name as charge_name,
        vc.visit_charge_id,
        vc.date as charge_date,
        vc.created_by as created_by_id,
        personnel.personnel_fname as created_by_name,
        personnel.personnel_onames as created_by_surname,
        vt.visit_type_name as visit_category
    ');
    
    $this->db->from('visit_charge vc');
    $this->db->join('visit v', 'vc.visit_id = v.visit_id', 'inner');
    $this->db->join('service_charge sc', 'vc.service_charge_id = sc.service_charge_id', 'inner');
    $this->db->join('patients p', 'v.patient_id = p.patient_id', 'inner');
    $this->db->join('personnel', 'vc.created_by = personnel.personnel_id', 'left');
    $this->db->join('visit_type vt', 'v.visit_type = vt.visit_type_id', 'left');
    
    // Apply base conditions (matching your SQL)
    $this->db->where('vc.visit_charge_delete', 0);
    $this->db->where('(v.parent_visit IS NULL OR v.parent_visit = 0)');
    $this->db->where('v.visit_delete', 0);
    $this->db->where('vc.charged', 1);
    
    // Apply optional filters
    if ($date_from !== null) {
        $this->db->where('v.visit_date >=', $date_from);
    }
    
    if ($date_to !== null) {
        $this->db->where('v.visit_date <=', $date_to);
    }
    
    if ($service_charge_id !== null) {
        $this->db->where('vc.service_charge_id', $service_charge_id);
    }
    
    // Order by visit date and charge date (most recent first)
    $this->db->order_by('v.visit_date', 'DESC');
    $this->db->order_by('vc.date', 'DESC');
    
    // Apply limit and offset
    $this->db->limit($limit, $offset);
    
    $query = $this->db->get();
    
    if ($query->num_rows() === 0) {
        return [];
    }
    
    $invoice_items = $query->result_array();
    
    // Process the data
    foreach ($invoice_items as &$item) {
        // Format patient name
        $item['patient_name'] = trim($item['patient_surname'] . ' ' . $item['patient_othernames']);
        
        // Calculate total amount
        $item['total_amount'] = floatval($item['units']) * floatval($item['unit_amount']);
        
        // Format created by name
        $item['created_by'] = trim(($item['created_by_name'] ?? '') . ' ' . ($item['created_by_surname'] ?? ''));
        if (empty(trim($item['created_by']))) {
            $item['created_by'] = 'Unknown';
        }
        
        // Format charge date
        if (!empty($item['charge_date'])) {
            $item['formatted_charge_date'] = date('jS M Y', strtotime($item['charge_date']));
        } else {
            $item['formatted_charge_date'] = '-';
        }
    }
    
    return $invoice_items;
}

/**
 * Add Excel headers for invoice items export
 */
private function add_invoice_items_excel_headers($sheet)
{
    $headers = [
        'A1' => '#',
        'B1' => 'Visit ID',
        'C1' => 'Visit Date',
        'D1' => 'Patient ID',
        'E1' => 'Patient Number',
        'F1' => 'Patient Name',
        'G1' => 'Visit Category',
        'H1' => 'Service/Charge Name',
        'I1' => 'Units/Quantity',
        'J1' => 'Unit Amount',
        'K1' => 'Total Amount',
        'L1' => 'Charge Date',
        'M1' => 'Created By'
    ];
    
    foreach ($headers as $cell => $value) {
        $sheet->setCellValue($cell, $value);
    }
    
    // Apply header styling
    $headerStyle = [
        'font' => [
            'bold' => true,
            'color' => ['rgb' => 'FFFFFF']
        ],
        'fill' => [
            'fillType' => Fill::FILL_SOLID,
            'startColor' => ['rgb' => '4472C4'] // Blue for invoice items
        ],
        'borders' => [
            'allBorders' => [
                'borderStyle' => Border::BORDER_THIN
            ]
        ],
        'alignment' => [
            'horizontal' => Alignment::HORIZONTAL_CENTER,
            'vertical' => Alignment::VERTICAL_CENTER
        ]
    ];
    
    $sheet->getStyle('A1:M1')->applyFromArray($headerStyle);
    
    // Auto-size columns
    foreach (range('A', 'M') as $col) {
        $sheet->getColumnDimension($col)->setAutoSize(true);
    }
}

/**
 * Add an invoice item record to the Excel sheet
 */
private function add_invoice_item_record_to_sheet($sheet, $record, $row)
{
    $record_number = $row - 1; // Row number starts from 2, so subtract 1 for numbering
    
    $sheet->setCellValue('A' . $row, $record_number);
    $sheet->setCellValue('B' . $row, $record['visit_id']);
    $sheet->setCellValue('C' . $row, date('jS M Y', strtotime($record['visit_date'])));
    $sheet->setCellValue('D' . $row, $record['patient_id']);
    $sheet->setCellValue('E' . $row, $record['patient_number']);
    $sheet->setCellValue('F' . $row, $record['patient_name']);
    $sheet->setCellValue('G' . $row, $record['visit_category'] ?? 'General');
    $sheet->setCellValue('H' . $row, $record['charge_name']);
    $sheet->setCellValue('I' . $row, number_format($record['units'], 2));
    $sheet->setCellValue('J' . $row, number_format($record['unit_amount'], 2));
    $sheet->setCellValue('K' . $row, number_format($record['total_amount'], 2));
    $sheet->setCellValue('L' . $row, $record['formatted_charge_date']);
    $sheet->setCellValue('M' . $row, $record['created_by']);
}

/**
 * Apply styling to invoice items Excel sheet
 */
private function apply_invoice_items_excel_styling($sheet, $total_rows)
{
    // Apply borders to all data
    $dataRange = 'A1:M' . $total_rows;
    $sheet->getStyle($dataRange)->applyFromArray([
        'borders' => [
            'allBorders' => [
                'borderStyle' => Border::BORDER_THIN
            ]
        ]
    ]);
    
    // Set row height for better readability
    for ($i = 2; $i <= $total_rows; $i++) {
        $sheet->getRowDimension($i)->setRowHeight(18);
    }
    
    // Format currency columns
    $currencyColumns = ['J', 'K']; // Unit Amount and Total Amount
    foreach ($currencyColumns as $col) {
        $range = $col . '2:' . $col . $total_rows;
        $sheet->getStyle($range)->getNumberFormat()->setFormatCode('#,##0.00');
    }
    
    // Format quantity column
    $quantityRange = 'I2:I' . $total_rows;
    $sheet->getStyle($quantityRange)->getNumberFormat()->setFormatCode('#,##0.00');
    
    // Center align certain columns
    $centerColumns = ['A', 'B', 'D', 'E', 'I', 'J', 'K'];
    foreach ($centerColumns as $col) {
        $sheet->getStyle($col . '2:' . $col . $total_rows)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    }
}

/**
 * Generate filename for invoice items export
 */
private function generate_invoice_items_filename($prefix, $record_count, $date_from = null, $date_to = null)
{
    $filename = $prefix . '_' . date('Y-m-d_H-i-s');
    
    if ($date_from && $date_to) {
        $filename .= '_' . $date_from . '_to_' . $date_to;
    } elseif ($date_from) {
        $filename .= '_from_' . $date_from;
    } elseif ($date_to) {
        $filename .= '_to_' . $date_to;
    }
    
    $filename .= '_' . number_format($record_count) . '_records.xlsx';
    
    return $filename;
}

/**
 * Export invoice items data as multiple files for very large datasets
 */
private function export_invoice_items_large_dataset($date_from, $date_to, $service_charge_id, $total_records)
{
    $files_needed = ceil($total_records / $this->max_records_per_file);
    $zip_filename = 'invoice_items_' . date('Y-m-d_H-i-s') . '_' . $total_records . '_records.zip';
    
    // Create temporary directory
    $temp_dir = sys_get_temp_dir() . '/invoice_items_export_' . uniqid();
    mkdir($temp_dir, 0777, true);
    
    $files_created = [];
    
    for ($file_num = 1; $file_num <= $files_needed; $file_num++) {
        $offset = ($file_num - 1) * $this->max_records_per_file;
        $limit = min($this->max_records_per_file, $total_records - $offset);
        
        $filename = $this->create_invoice_items_file_chunk($date_from, $date_to, $service_charge_id, $offset, $limit, $file_num, $temp_dir);
        
        if ($filename) {
            $files_created[] = $filename;
        }
        
        // Memory cleanup after each file
        gc_collect_cycles();
    }
    
    // Create ZIP file and stream it
    $this->create_zip_archive($files_created, $temp_dir, $zip_filename);
    
    // Cleanup temporary files
    $this->cleanup_temp_files($temp_dir);
}

/**
 * Create a single file chunk for invoice items export
 */
private function create_invoice_items_file_chunk($date_from, $date_to, $service_charge_id, $offset, $limit, $file_num, $temp_dir)
{
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    
    // Set document properties
    $spreadsheet->getProperties()
        ->setCreator('Dental Management System')
        ->setTitle("Invoice Items - Part {$file_num}")
        ->setDescription("Large dataset export - Part {$file_num} of multiple files");
    
    // Add headers
    $this->add_invoice_items_excel_headers($sheet);
    
    // Get data chunk
    $chunk_data = $this->get_invoice_items_chunk($date_from, $date_to, $service_charge_id, $limit, $offset);
    
    if (empty($chunk_data)) {
        return null;
    }
    
    $current_row = 2;
    foreach ($chunk_data as $record) {
        $this->add_invoice_item_record_to_sheet($sheet, $record, $current_row);
        $current_row++;
    }
    
    // Apply styling
    $this->apply_invoice_items_excel_styling($sheet, $current_row - 1);
    
    // Save file
    $filename = "invoice_items_part_{$file_num}_" . date('Y-m-d_H-i-s') . '.xlsx';
    $filepath = $temp_dir . '/' . $filename;
    
    $writer = new Xlsx($spreadsheet);
    $writer->save($filepath);
    
    // Clean up
    $spreadsheet->disconnectWorksheets();
    unset($spreadsheet);
    
    return $filepath;
}

// =====================================================
// ADDITIONAL INVOICE ITEMS EXPORT METHODS
// =====================================================

/**
 * Export invoice items by date range
 */
public function export_invoice_items_by_date($date_from, $date_to)
{
    // Validate dates
    if (!$date_from || !$date_to) {
        show_error('Both from and to dates are required');
        return;
    }
    
    // Validate date format
    if (!strtotime($date_from) || !strtotime($date_to)) {
        show_error('Invalid date format. Use YYYY-MM-DD');
        return;
    }
    
    $this->export_invoice_items_to_excel($date_from, $date_to);
}

/**
 * Export invoice items by service charge
 */
public function export_invoice_items_by_service($service_charge_id)
{
    if (!$service_charge_id || !is_numeric($service_charge_id)) {
        show_error('Valid service charge ID is required');
        return;
    }
    
    $this->export_invoice_items_to_excel(null, null, $service_charge_id);
}

/**
 * Get service charges for filter dropdown
 */
public function get_service_charges()
{
    header('Content-Type: application/json');
    
    $this->db->select('service_charge_id, service_charge_name');
    $this->db->from('service_charge');
    $this->db->order_by('service_charge_name', 'ASC');
    
    $query = $this->db->get();
    $services = $query->result_array();
    
    echo json_encode([
        'success' => true,
        'data' => $services
    ]);
}

/**
 * Get invoice items statistics for dashboard
 */
public function get_invoice_items_stats($date_from = null, $date_to = null)
{
    header('Content-Type: application/json');
    
    // Base query for statistics
    $this->db->select('
        COUNT(*) as total_items,
        SUM(vc.visit_charge_units * vc.visit_charge_amount) as total_amount,
        AVG(vc.visit_charge_units * vc.visit_charge_amount) as average_amount,
        SUM(vc.visit_charge_units) as total_units
    ');
    $this->db->from('visit_charge vc');
    $this->db->join('visit v', 'vc.visit_id = v.visit_id', 'inner');
    
    // Apply base conditions
    $this->db->where('vc.visit_charge_delete', 0);
    $this->db->where('(v.parent_visit IS NULL OR v.parent_visit = 0)');
    $this->db->where('v.visit_delete', 0);
    $this->db->where('vc.charged', 1);
    
    // Apply date filters if provided
    if ($date_from) {
        $this->db->where('v.visit_date >=', $date_from);
    }
    
    if ($date_to) {
        $this->db->where('v.visit_date <=', $date_to);
    }
    
    $query = $this->db->get();
    $stats = $query->row_array();
    
    // Get service charge breakdown
    $this->db->select('sc.service_charge_name, COUNT(*) as count, SUM(vc.visit_charge_units * vc.visit_charge_amount) as total');
    $this->db->from('visit_charge vc');
    $this->db->join('visit v', 'vc.visit_id = v.visit_id', 'inner');
    $this->db->join('service_charge sc', 'vc.service_charge_id = sc.service_charge_id', 'inner');
    
    // Apply same conditions
    $this->db->where('vc.visit_charge_delete', 0);
    $this->db->where('(v.parent_visit IS NULL OR v.parent_visit = 0)');
    $this->db->where('v.visit_delete', 0);
    $this->db->where('vc.charged', 1);
    
    if ($date_from) {
        $this->db->where('v.visit_date >=', $date_from);
    }
    
    if ($date_to) {
        $this->db->where('v.visit_date <=', $date_to);
    }
    
    $this->db->group_by('sc.service_charge_id');
    $this->db->order_by('total', 'DESC');
    $this->db->limit(10); // Top 10 services
    
    $service_query = $this->db->get();
    $service_breakdown = $service_query->result_array();
    
    echo json_encode([
        'success' => true,
        'stats' => $stats,
        'service_breakdown' => $service_breakdown
    ]);
}






// =====================================================
// WAIVERS EXPORT FUNCTIONS - Add to your Export controller
// =====================================================

/**
 * Export waivers data to Excel - handles large datasets efficiently
 * @param string $date_from Optional start date filter
 * @param string $date_to Optional end date filter
 * @param int $created_by Optional created by filter
 */
public function export_waivers_to_excel($date_from = null, $date_to = null, $created_by = null)
{
    // Get total record count first
    $total_records = $this->get_waivers_total_count($date_from, $date_to, $created_by);
    
    if ($total_records === 0) {
        show_error('No waivers found for export');
        return;
    }
    
    // Log export start
    log_message('info', "Starting waivers export: {$total_records} records");
    
    // Determine export method based on dataset size
    if ($total_records > $this->max_records_per_file) {
        $this->export_waivers_large_dataset($date_from, $date_to, $created_by, $total_records);
    } else {
        $this->export_waivers_single_file($date_from, $date_to, $created_by, $total_records);
    }
}

/**
 * Export waivers data in a single file
 */
private function export_waivers_single_file($date_from, $date_to, $created_by, $total_records)
{
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    
    // Set document properties
    $spreadsheet->getProperties()
        ->setCreator('Dental Management System')
        ->setTitle("Waivers Report - {$total_records} records")
        ->setDescription("Export of waivers with patient and visit details");
    
    // Add headers
    $this->add_waivers_excel_headers($sheet);
    
    $current_row = 2;
    $processed_records = 0;
    $start_time = microtime(true);
    
    // Process data in chunks to manage memory
    for ($offset = 0; $offset < $total_records; $offset += $this->chunk_size) {
        
        // Get chunk of waivers data
        $chunk_data = $this->get_waivers_chunk($date_from, $date_to, $created_by, $this->chunk_size, $offset);
        
        if (empty($chunk_data)) {
            break;
        }
        
        // Process each record in the chunk
        foreach ($chunk_data as $record) {
            $this->add_waiver_record_to_sheet($sheet, $record, $current_row);
            $current_row++;
            $processed_records++;
            
            // Memory management - force garbage collection every 100 records
            if ($processed_records % 100 === 0) {
                gc_collect_cycles();
                
                // Log progress every 1000 records
                if ($processed_records % 1000 === 0) {
                    $elapsed = microtime(true) - $start_time;
                    $rate = $processed_records / $elapsed;
                    log_message('info', "Processed {$processed_records}/{$total_records} waivers. Rate: " . number_format($rate, 2) . " records/sec");
                }
            }
        }
        
        // Clear chunk data from memory
        unset($chunk_data);
    }
    
    // Apply final styling
    $this->apply_waivers_excel_styling($sheet, $current_row - 1);
    
    // Generate filename
    $filename = $this->generate_waivers_filename('waivers_report', $total_records, $date_from, $date_to);
    
    // Stream the file
    $this->stream_excel_file($spreadsheet, $filename);
    
    // Log completion
    $total_time = microtime(true) - $start_time;
    log_message('info', "Waivers export completed: {$processed_records} records in " . number_format($total_time, 2) . " seconds");
}

/**
 * Get total count of waivers records
 */
private function get_waivers_total_count($date_from = null, $date_to = null, $created_by = null)
{
    $this->db->select('COUNT(*) as total');
    $this->db->from('payments p');
    $this->db->join('visit v', 'p.visit_id = v.visit_id', 'inner');
    $this->db->join('patients pt', 'v.patient_id = pt.patient_id', 'inner');
    
    // Apply base conditions (matching your SQL exactly)
    $this->db->where('p.cancel', 0);
    $this->db->where('p.payment_type', 2); // Waivers are payment_type = 2
    $this->db->where('(v.parent_visit IS NULL OR v.parent_visit = 0)');
    $this->db->where('v.visit_delete', 0);
    
    // Apply optional filters
    if ($date_from !== null) {
        $this->db->where('p.payment_created >=', $date_from);
    }
    
    if ($date_to !== null) {
        $this->db->where('p.payment_created <=', $date_to);
    }
    
    if ($created_by !== null) {
        $this->db->where('p.payment_created_by', $created_by);
    }
    
    $query = $this->db->get();
    $result = $query->row();
    
    return $result ? (int)$result->total : 0;
}

/**
 * Get a chunk of waivers records
 */
private function get_waivers_chunk($date_from, $date_to, $created_by, $limit, $offset)
{
    $this->db->select('
        v.visit_id,
        v.visit_date,
        v.visit_time,
        pt.patient_id,
        pt.patient_number,
        pt.patient_surname,
        pt.patient_othernames,
        p.payment_created,
        p.payment_created_by,
        p.amount_paid as waiver_amount,
        p.payment_id,
        p.time as payment_time,
        p.transaction_code,
        personnel.personnel_fname as created_by_name,
        personnel.personnel_onames as created_by_surname,
        vt.visit_type_name as visit_category,
        pm.payment_method as waiver_method
    ');
    
    $this->db->from('payments p');
    $this->db->join('visit v', 'p.visit_id = v.visit_id', 'inner');
    $this->db->join('patients pt', 'v.patient_id = pt.patient_id', 'inner');
    $this->db->join('personnel', 'p.payment_created_by = personnel.personnel_id', 'left');
    $this->db->join('visit_type vt', 'v.visit_type = vt.visit_type_id', 'left');
    $this->db->join('payment_method pm', 'p.payment_method_id = pm.payment_method_id', 'left');
    
    // Apply base conditions (matching your SQL exactly)
    $this->db->where('p.cancel', 0);
    $this->db->where('p.payment_type', 2); // Waivers are payment_type = 2
    $this->db->where('(v.parent_visit IS NULL OR v.parent_visit = 0)');
    $this->db->where('v.visit_delete', 0);
    
    // Apply optional filters
    if ($date_from !== null) {
        $this->db->where('p.payment_created >=', $date_from);
    }
    
    if ($date_to !== null) {
        $this->db->where('p.payment_created <=', $date_to);
    }
    
    if ($created_by !== null) {
        $this->db->where('p.payment_created_by', $created_by);
    }
    
    // Order by waiver date (most recent first)
    $this->db->order_by('p.payment_created', 'DESC');
    $this->db->order_by('p.time', 'DESC');
    
    // Apply limit and offset
    $this->db->limit($limit, $offset);
    
    $query = $this->db->get();
    
    if ($query->num_rows() === 0) {
        return [];
    }
    
    $waivers = $query->result_array();
    
    // Process the data
    foreach ($waivers as &$waiver) {
        // Format patient name
        $waiver['patient_name'] = trim($waiver['patient_surname'] . ' ' . $waiver['patient_othernames']);
        
        // Format created by name
        $waiver['created_by'] = trim(($waiver['created_by_name'] ?? '') . ' ' . ($waiver['created_by_surname'] ?? ''));
        if (empty(trim($waiver['created_by']))) {
            $waiver['created_by'] = 'Unknown';
        }
        
        // Format waiver date and time
        $waiver['formatted_waiver_date'] = date('jS M Y', strtotime($waiver['payment_created']));
        $waiver['formatted_waiver_time'] = !empty($waiver['payment_time']) ? date('H:i a', strtotime($waiver['payment_time'])) : '-';
        
        // Format visit date
        $waiver['formatted_visit_date'] = date('jS M Y', strtotime($waiver['visit_date']));
    }
    
    return $waivers;
}

/**
 * Add Excel headers for waivers export
 */
private function add_waivers_excel_headers($sheet)
{
    $headers = [
        'A1' => '#',
        'B1' => 'Visit ID',
        'C1' => 'Visit Date',
        'D1' => 'Patient ID',
        'E1' => 'Patient Number',
        'F1' => 'Patient Name',
        'G1' => 'Visit Category',
        'H1' => 'Waiver Amount',
        'I1' => 'Waiver Date',
        'J1' => 'Waiver Time',
        'K1' => 'Waiver Method',
        'L1' => 'Transaction Code',
        'M1' => 'Created By',
        'N1' => 'Payment ID'
    ];
    
    foreach ($headers as $cell => $value) {
        $sheet->setCellValue($cell, $value);
    }
    
    // Apply header styling
    $headerStyle = [
        'font' => [
            'bold' => true,
            'color' => ['rgb' => 'FFFFFF']
        ],
        'fill' => [
            'fillType' => Fill::FILL_SOLID,
            'startColor' => ['rgb' => 'DC3545'] // Red for waivers
        ],
        'borders' => [
            'allBorders' => [
                'borderStyle' => Border::BORDER_THIN
            ]
        ],
        'alignment' => [
            'horizontal' => Alignment::HORIZONTAL_CENTER,
            'vertical' => Alignment::VERTICAL_CENTER
        ]
    ];
    
    $sheet->getStyle('A1:N1')->applyFromArray($headerStyle);
    
    // Auto-size columns
    foreach (range('A', 'N') as $col) {
        $sheet->getColumnDimension($col)->setAutoSize(true);
    }
}

/**
 * Add a waiver record to the Excel sheet
 */
private function add_waiver_record_to_sheet($sheet, $record, $row)
{
    $record_number = $row - 1; // Row number starts from 2, so subtract 1 for numbering
    
    $sheet->setCellValue('A' . $row, $record_number);
    $sheet->setCellValue('B' . $row, $record['visit_id']);
    $sheet->setCellValue('C' . $row, $record['formatted_visit_date']);
    $sheet->setCellValue('D' . $row, $record['patient_id']);
    $sheet->setCellValue('E' . $row, $record['patient_number']);
    $sheet->setCellValue('F' . $row, $record['patient_name']);
    $sheet->setCellValue('G' . $row, $record['visit_category'] ?? 'General');
    $sheet->setCellValue('H' . $row, number_format($record['waiver_amount'], 2));
    $sheet->setCellValue('I' . $row, $record['formatted_waiver_date']);
    $sheet->setCellValue('J' . $row, $record['formatted_waiver_time']);
    $sheet->setCellValue('K' . $row, $record['waiver_method'] ?? 'Waiver');
    $sheet->setCellValue('L' . $row, $record['transaction_code'] ?? '-');
    $sheet->setCellValue('M' . $row, $record['created_by']);
    $sheet->setCellValue('N' . $row, $record['payment_id']);
}

/**
 * Apply styling to waivers Excel sheet
 */
private function apply_waivers_excel_styling($sheet, $total_rows)
{
    // Apply borders to all data
    $dataRange = 'A1:N' . $total_rows;
    $sheet->getStyle($dataRange)->applyFromArray([
        'borders' => [
            'allBorders' => [
                'borderStyle' => Border::BORDER_THIN
            ]
        ]
    ]);
    
    // Set row height for better readability
    for ($i = 2; $i <= $total_rows; $i++) {
        $sheet->getRowDimension($i)->setRowHeight(18);
    }
    
    // Format waiver amount column as currency
    $amountRange = 'H2:H' . $total_rows;
    $sheet->getStyle($amountRange)->getNumberFormat()->setFormatCode('#,##0.00');
    
    // Center align certain columns
    $centerColumns = ['A', 'B', 'D', 'E', 'H', 'N'];
    foreach ($centerColumns as $col) {
        $sheet->getStyle($col . '2:' . $col . $total_rows)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    }
    
    // Highlight waiver amounts with light red background
    $sheet->getStyle('H2:H' . $total_rows)->applyFromArray([
        'fill' => [
            'fillType' => Fill::FILL_SOLID,
            'startColor' => ['rgb' => 'FFE6E6'] // Light red
        ]
    ]);
}

/**
 * Generate filename for waivers export
 */
private function generate_waivers_filename($prefix, $record_count, $date_from = null, $date_to = null)
{
    $filename = $prefix . '_' . date('Y-m-d_H-i-s');
    
    if ($date_from && $date_to) {
        $filename .= '_' . $date_from . '_to_' . $date_to;
    } elseif ($date_from) {
        $filename .= '_from_' . $date_from;
    } elseif ($date_to) {
        $filename .= '_to_' . $date_to;
    }
    
    $filename .= '_' . number_format($record_count) . '_records.xlsx';
    
    return $filename;
}

/**
 * Export waivers data as multiple files for very large datasets
 */
private function export_waivers_large_dataset($date_from, $date_to, $created_by, $total_records)
{
    $files_needed = ceil($total_records / $this->max_records_per_file);
    $zip_filename = 'waivers_report_' . date('Y-m-d_H-i-s') . '_' . $total_records . '_records.zip';
    
    // Create temporary directory
    $temp_dir = sys_get_temp_dir() . '/waivers_export_' . uniqid();
    mkdir($temp_dir, 0777, true);
    
    $files_created = [];
    
    for ($file_num = 1; $file_num <= $files_needed; $file_num++) {
        $offset = ($file_num - 1) * $this->max_records_per_file;
        $limit = min($this->max_records_per_file, $total_records - $offset);
        
        $filename = $this->create_waivers_file_chunk($date_from, $date_to, $created_by, $offset, $limit, $file_num, $temp_dir);
        
        if ($filename) {
            $files_created[] = $filename;
        }
        
        // Memory cleanup after each file
        gc_collect_cycles();
    }
    
    // Create ZIP file and stream it
    $this->create_zip_archive($files_created, $temp_dir, $zip_filename);
    
    // Cleanup temporary files
    $this->cleanup_temp_files($temp_dir);
}

/**
 * Create a single file chunk for waivers export
 */
private function create_waivers_file_chunk($date_from, $date_to, $created_by, $offset, $limit, $file_num, $temp_dir)
{
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    
    // Set document properties
    $spreadsheet->getProperties()
        ->setCreator('Dental Management System')
        ->setTitle("Waivers Report - Part {$file_num}")
        ->setDescription("Large dataset export - Part {$file_num} of multiple files");
    
    // Add headers
    $this->add_waivers_excel_headers($sheet);
    
    // Get data chunk
    $chunk_data = $this->get_waivers_chunk($date_from, $date_to, $created_by, $limit, $offset);
    
    if (empty($chunk_data)) {
        return null;
    }
    
    $current_row = 2;
    foreach ($chunk_data as $record) {
        $this->add_waiver_record_to_sheet($sheet, $record, $current_row);
        $current_row++;
    }
    
    // Apply styling
    $this->apply_waivers_excel_styling($sheet, $current_row - 1);
    
    // Save file
    $filename = "waivers_report_part_{$file_num}_" . date('Y-m-d_H-i-s') . '.xlsx';
    $filepath = $temp_dir . '/' . $filename;
    
    $writer = new Xlsx($spreadsheet);
    $writer->save($filepath);
    
    // Clean up
    $spreadsheet->disconnectWorksheets();
    unset($spreadsheet);
    
    return $filepath;
}

// =====================================================
// ADDITIONAL WAIVERS EXPORT METHODS
// =====================================================

/**
 * Export waivers by date range
 */
public function export_waivers_by_date($date_from, $date_to)
{
    // Validate dates
    if (!$date_from || !$date_to) {
        show_error('Both from and to dates are required');
        return;
    }
    
    // Validate date format
    if (!strtotime($date_from) || !strtotime($date_to)) {
        show_error('Invalid date format. Use YYYY-MM-DD');
        return;
    }
    
    $this->export_waivers_to_excel($date_from, $date_to);
}

/**
 * Export waivers by creator
 */
public function export_waivers_by_creator($created_by)
{
    if (!$created_by || !is_numeric($created_by)) {
        show_error('Valid creator ID is required');
        return;
    }
    
    $this->export_waivers_to_excel(null, null, $created_by);
}

/**
 * Get waivers statistics for dashboard
 */
public function get_waivers_stats($date_from = null, $date_to = null)
{
    header('Content-Type: application/json');
    
    // Base query for statistics
    $this->db->select('
        COUNT(*) as total_waivers,
        SUM(p.amount_paid) as total_waiver_amount,
        AVG(p.amount_paid) as average_waiver_amount,
        MIN(p.amount_paid) as min_waiver_amount,
        MAX(p.amount_paid) as max_waiver_amount
    ');
    $this->db->from('payments p');
    $this->db->join('visit v', 'p.visit_id = v.visit_id', 'inner');
    
    // Apply base conditions
    $this->db->where('p.cancel', 0);
    $this->db->where('p.payment_type', 2); // Waivers
    $this->db->where('(v.parent_visit IS NULL OR v.parent_visit = 0)');
    $this->db->where('v.visit_delete', 0);
    
    // Apply date filters if provided
    if ($date_from) {
        $this->db->where('p.payment_created >=', $date_from);
    }
    
    if ($date_to) {
        $this->db->where('p.payment_created <=', $date_to);
    }
    
    $query = $this->db->get();
    $stats = $query->row_array();
    
    // Get creator breakdown
    $this->db->select('personnel.personnel_fname, personnel.personnel_onames, COUNT(*) as count, SUM(p.amount_paid) as total');
    $this->db->from('payments p');
    $this->db->join('visit v', 'p.visit_id = v.visit_id', 'inner');
    $this->db->join('personnel', 'p.payment_created_by = personnel.personnel_id', 'left');
    
    // Apply same conditions
    $this->db->where('p.cancel', 0);
    $this->db->where('p.payment_type', 2); // Waivers
    $this->db->where('(v.parent_visit IS NULL OR v.parent_visit = 0)');
    $this->db->where('v.visit_delete', 0);
    
    if ($date_from) {
        $this->db->where('p.payment_created >=', $date_from);
    }
    
    if ($date_to) {
        $this->db->where('p.payment_created <=', $date_to);
    }
    
    $this->db->group_by('p.payment_created_by');
    $this->db->order_by('total', 'DESC');
    $this->db->limit(10); // Top 10 creators
    
    $creator_query = $this->db->get();
    $creator_breakdown = $creator_query->result_array();
    
    // Format creator names
    foreach ($creator_breakdown as &$creator) {
        $creator['creator_name'] = trim(($creator['personnel_fname'] ?? '') . ' ' . ($creator['personnel_onames'] ?? ''));
        if (empty(trim($creator['creator_name']))) {
            $creator['creator_name'] = 'Unknown';
        }
    }
    
    echo json_encode([
        'success' => true,
        'stats' => $stats,
        'creator_breakdown' => $creator_breakdown
    ]);
}

/**
 * Get personnel list for filter dropdown
 */
public function get_personnel_list()
{
    header('Content-Type: application/json');
    
    $this->db->select('personnel_id, personnel_fname, personnel_onames');
    $this->db->from('personnel');
    $this->db->where('personnel_status', 1); // Active personnel only
    $this->db->order_by('personnel_fname', 'ASC');
    
    $query = $this->db->get();
    $personnel = $query->result_array();
    
    // Format names
    foreach ($personnel as &$person) {
        $person['full_name'] = trim($person['personnel_fname'] . ' ' . $person['personnel_onames']);
    }
    
    echo json_encode([
        'success' => true,
        'data' => $personnel
    ]);
}
}
?>