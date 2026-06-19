<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Carbon\Carbon;

class FoDocumentsExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles, WithColumnWidths
{
    protected $documents;
    protected $filters;
    protected $rowNumber = 0;

    public function __construct($documents, $filters = [])
    {
        $this->documents = $documents;
        $this->filters = $filters;
    }

    public function collection()
    {
        return $this->documents;
    }

    public function headings(): array
    {
        return [
            'S.No',
            'Document ID',
            'From Department',
            'Expenditure ID',
            'Title',
            'Expenditure Category',            
            'Budget',
            'Paid Amount',
            'TDS',
            'Payment Cleared Date',
            'Mode of Payment',
            'Reference Number',
            'Balance to be Paid',
            'Full/Partial Payment',
            'File Status',
            'Remarks',
            'Priority',
            'Payment Status',
            'Bill Amount',           // NEW
            'Refund Amount',         // NEW
            'Bill Submission Date',  // NEW
            'Refund Date',           // NEW
            'Document Created Date',
            'Forwarded Date',
        ];
    }

    public function map($document): array
    {
        $this->rowNumber++;
        
        // Format expenditure IDs (take first one if multiple)
        $expenditureIds = $this->extractFirstValue($document->expenditure_ids);
        
        // Format expenditure categories (take first one if multiple)
        $expenditureCategory = $this->extractFirstValue($document->expenditure_categories);
        
        // Format payment modes (take first one if multiple)
        $paymentMode = $this->extractFirstValue($document->payment_modes);
        
        // Format reference numbers (take first one if multiple)
        $referenceNumber = $this->extractFirstValue($document->reference_numbers);
        
        // Format payment types (determine if full or partial)
        $paymentType = $this->determinePaymentType(
            $document->budget_amount,
            $document->total_paid_amount,
            $document->payment_types
        );
        
        // Calculate balance to be paid
        $totalPaidIncludingTDS = $document->total_paid_amount + $document->total_tds_amount;
        $balance = $document->budget_amount - $totalPaidIncludingTDS;
        
        // Format dates
        $docCreatedDate = $document->doc_created_at 
            ? Carbon::parse($document->doc_created_at)->format('d-m-Y')
            : 'N/A';
            
        $forwardedDate = $document->forwarded_date 
            ? Carbon::parse($document->forwarded_date)->format('d-m-Y')
            : 'N/A';
            
        $paymentDate = $document->latest_payment_date 
            ? Carbon::parse($document->latest_payment_date)->format('d-m-Y')
            : 'N/A';

        $billSubmissionDate = $document->latest_bill_submission_date 
            ? Carbon::parse($document->latest_bill_submission_date)->format('d-m-Y')
            : 'N/A';
            
        $refundDate = $document->latest_refund_date 
            ? Carbon::parse($document->latest_refund_date)->format('d-m-Y')
            : 'N/A';

        // Get remarks (combine all remarks if multiple)
        $remarks = $document->payment_remarks ?? '';
        
        if (strlen($remarks) > 100) {
            $remarks = substr($remarks, 0, 97) . '...';
        }

        $remarks = str_replace('\n', "\n", $remarks);
        
        return [
            $this->rowNumber,
            $document->doc_id,
            $document->from,
            $expenditureIds ?: 'Not yet Assigned',
            $document->title,
            $expenditureCategory ?: 'N/A',
            number_format($document->budget_amount, 2),
            number_format($document->total_paid_amount, 2),
            number_format($document->total_tds_amount, 2),
            $paymentDate,
            $paymentMode ?: 'N/A',
            $referenceNumber ?: 'N/A',
            number_format(max(0, $balance), 2), // Ensure non-negative
            $paymentType,
            $document->file_status ?: 'N/A',
            $remarks,
            $document->priority,
            $document->payment_status ?: 'Not yet Assigned',
            number_format($document->total_bill_amount, 2), // NEW
            number_format($document->total_refund_amount, 2), // NEW
            $billSubmissionDate, // NEW
            $refundDate, // NEW
            $docCreatedDate,
            $forwardedDate
        ];
    }
    
    private function extractFirstValue($commaSeparatedValues)
    {
        if (empty($commaSeparatedValues)) {
            return null;
        }
        
        $values = explode(',', $commaSeparatedValues);
        return trim($values[0]);
    }
    
    private function determinePaymentType($budgetAmount, $totalPaid, $paymentTypes)
    {
        if (empty($paymentTypes)) {
            return 'No Payment';
        }
        
        $types = explode(',', $paymentTypes);
        $uniqueTypes = array_unique(array_map('trim', $types));
        
        if (count($uniqueTypes) > 1) {
            return 'Multiple Types';
        }
        
        $type = reset($uniqueTypes);
        
        // If budget is fully paid, it's Full regardless of payment_type
        $totalPaidWithTDS = $totalPaid; // Note: TDS is separate
        if ($budgetAmount > 0 && $totalPaidWithTDS >= $budgetAmount) {
            return 'Full';
        }
        
        return $type ?: 'Partial';
    }

    public function styles(Worksheet $sheet)
    {
        $lastRow = $sheet->getHighestRow();
        
        // Make first row bold
        $sheet->getStyle('A1:X1')->getFont()->setBold(true);
        
        // Add background color to header
        $sheet->getStyle('A1:X1')->getFill()
              ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
              ->getStartColor()->setARGB('FFE0E0E0');
        
        // Center align headers
        $sheet->getStyle('A1:X1')->getAlignment()->setHorizontal('center');
        
        // Add borders if there's data
        if ($lastRow > 1) {
            $sheet->getStyle('A1:X' . $lastRow)
                  ->getBorders()
                  ->getAllBorders()
                  ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
            
            // Right align numeric columns (Budget, Paid Amount, TDS, Balance)
            $sheet->getStyle('E2:K' . $lastRow)->getAlignment()->setHorizontal('right');
            
            // Format numeric columns with commas
            $sheet->getStyle('E2:G' . $lastRow)->getNumberFormat()->setFormatCode('#,##0.00');
            $sheet->getStyle('K2:K' . $lastRow)->getNumberFormat()->setFormatCode('#,##0.00');
        }
        
        // Freeze first row
        $sheet->freezePane('A2');
        
        // Add filter info as a note in cell A1
        if (!empty($this->filters)) {
            $filterText = "Exported with filters:\n";
            
            if (!empty($this->filters['title'])) {
                $filterText .= "Title: " . $this->filters['title'] . "\n";
            }
            if (!empty($this->filters['doc_id'])) {
                $filterText .= "Document ID: " . $this->filters['doc_id'] . "\n";
            }
            if (!empty($this->filters['date_from']) && !empty($this->filters['date_to'])) {
                $filterText .= "Date Range: " . $this->filters['date_from'] . " to " . $this->filters['date_to'] . "\n";
            }
            if (!empty($this->filters['priority'])) {
                $filterText .= "Priority: " . $this->filters['priority'] . "\n";
            }
            
            $sheet->getComment('A1')->getText()->createTextRun($filterText);
        }
        
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
    
    public function columnWidths(): array
    {
        return [
            'A' => 8,   // S.No 
            'B' => 15,  // Document ID 
            'C' => 20,  // From
            'D' => 20,  // Expenditure ID
            'E' => 30,  // Title
            'F' => 20,  // Expenditure Category
            'G' => 15,  // Budget
            'H' => 15,  // Paid Amount
            'I' => 12,  // TDS
            'J' => 20,  // Payment Cleared Date
            'K' => 15,  // Mode of Payment
            'L' => 20,  // Reference Number
            'M' => 18,  // Balance to be Paid
            'N' => 18,  // Full/Partial Payment
            'O' => 75,  // File Status
            'P' => 40,  // Remarks
            'Q' => 12,  // Priority
            'R' => 15,  // Payment Status
            'S' => 15,  // Bill Amount (NEW)
            'T' => 15,  // Refund Amount (NEW)
            'U' => 20,  // Bill Submission Date (NEW)
            'V' => 15,  // Refund Date (NEW)
            'W' => 20,  // Document Created Date
            'X' => 15,  // Forwarded Date
        ];
    }
}