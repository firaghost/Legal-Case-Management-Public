<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CaseTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('secured_loan_auctions')->truncate();
        DB::table('legal_advisories')->truncate();
        DB::table('legal_advisory_cases')->truncate();
        DB::table('secured_loan_recovery_cases')->truncate();
        DB::table('criminal_litigation_cases')->truncate();
        DB::table('other_civil_litigation_cases')->truncate();
        DB::table('labor_litigation_cases')->truncate();
        DB::table('clean_loan_recovery_cases')->truncate();
        DB::table('defendants')->truncate();
        DB::table('plaintiffs')->truncate();
        DB::table('case_files')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $caseTypes = [
            [
                'id' => 1,
                'name' => 'Clean Loan Recovery',
                'code' => '01',
                'description' => 'Clean loan recovery cases without collateral',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 2,
                'name' => 'Labor Litigation',
                'code' => '02',
                'description' => 'Labor and employment related litigation cases',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 3,
                'name' => 'Other Civil Litigation',
                'code' => '03',
                'description' => 'Other civil litigation cases',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 4,
                'name' => 'Criminal Litigation',
                'code' => '04',
                'description' => 'Criminal cases',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 5,
                'name' => 'Secured Loan Recovery',
                'code' => '05',
                'description' => 'Loan recovery cases with collateral',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 6,
                'name' => 'Legal Advisory',
                'code' => '06',
                'description' => 'Legal advisory and consultation services',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        // Fetch the lawyer user ID (defaults to first user if not found)
        $lawyerId = DB::table('users')->where('email', 'lawyer@lcms.test')->value('id') ?? DB::table('users')->min('id');
        
        // Fetch the admin user ID for created_by (defaults to first user if not found)
        $adminId = DB::table('users')->where('email', 'admin@lcms.test')->value('id') ?? DB::table('users')->min('id');
        
        // Get branch and work unit IDs
        $branchId = DB::table('branches')->first()->id ?? null;
        $workUnitId = DB::table('work_units')->first()->id ?? null;

        // Use updateOrInsert to prevent duplicates
        foreach ($caseTypes as $caseType) {
            DB::table('case_types')->updateOrInsert(
                ['id' => $caseType['id']],
                $caseType
            );
        }

        // ===== 1. CLEAN LOAN RECOVERY CASES =====
        $cleanLoanCases = [
            [
                'file_number' => 'CLN-LO-25001',
                'title' => 'Abebe Kebede - Unsecured Business Loan Recovery',
                'description' => 'Recovery of ETB 150,000 business expansion loan without collateral. Borrower defaulted after 6 months.',
                'outstanding_amount' => 150000,
                'claimed_amount' => 165000, // includes interest and fees
                'recovered_amount' => 35000,
                'defendant' => [
                    'name' => 'Abebe Kebede',
                    'contact_number' => '+1-555-0101',
                    'address' => 'Kirkos Sub-City, Kebele 08, Addis Ababa',
                    'email' => 'abebe.k@email.com'
                ]
            ],
            [
                'file_number' => 'CLN-LO-25002', 
                'title' => 'Tigist Mekonnen - Personal Loan Default',
                'description' => 'Personal loan for medical expenses. Payment stopped after borrower lost employment.',
                'outstanding_amount' => 85000,
                'claimed_amount' => 92000,
                'recovered_amount' => 15000,
                'defendant' => [
                    'name' => 'Tigist Mekonnen',
                    'contact_number' => '+1-555-0102',
                    'address' => 'Arada Sub-City, Kebele 03, Addis Ababa',
                    'email' => 'tigist.m@yahoo.com'
                ]
            ],
            [
                'file_number' => 'CLN-LO-25003',
                'title' => 'Mulugeta Bekele - Trade Finance Recovery', 
                'description' => 'Import trade financing for electronics. Goods stuck at customs, payment defaulted.',
                'outstanding_amount' => 280000,
                'claimed_amount' => 295000,
                'recovered_amount' => 80000,
                'defendant' => [
                    'name' => 'Mulugeta Bekele',
                    'contact_number' => '+1-555-0103',
                    'address' => 'Lideta Sub-City, Kebele 05, Addis Ababa',
                    'email' => 'mulugeta.trading@gmail.com'
                ]
            ]
        ];

        foreach ($cleanLoanCases as $caseData) {
            $caseId = DB::table('case_files')->insertGetId([
                'file_number' => $caseData['file_number'],
                'title' => $caseData['title'],
                'description' => $caseData['description'],
                'case_type_id' => 1,
                'status' => 'Open',
                'opened_at' => Carbon::now()->subDays(rand(30, 180)),
                'branch_id' => $branchId,
                'work_unit_id' => $workUnitId,
                'court_name' => 'Federal First Instance Court',
                'created_by' => $adminId,
                'lawyer_id' => $lawyerId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Insert plaintiff (Legal Organization)
            DB::table('plaintiffs')->insert([
                'case_file_id' => $caseId,
                'name' => 'Legal Organization',
                'contact_number' => '+1-555-0100',
                'address' => 'Legal Plaza, Main Building, Central District, Addis Ababa',
                'email' => 'legal@legalorg.example.com',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Insert defendant
            DB::table('defendants')->insert([
                'case_file_id' => $caseId,
                'name' => $caseData['defendant']['name'],
                'contact_number' => $caseData['defendant']['contact_number'],
                'address' => $caseData['defendant']['address'],
                'email' => $caseData['defendant']['email'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Insert clean loan recovery case details
            DB::table('clean_loan_recovery_cases')->insert([
                'case_file_id' => $caseId,
                'branch_id' => $branchId,
                'work_unit_id' => $workUnitId,
                'company_file_number' => 'CLR-' . now()->format('Y') . '-' . str_pad($loop->iteration ?? 1, 4, '0', STR_PAD_LEFT),
                'customer_name' => $caseData['defendant']['name'],
                'outstanding_amount' => $caseData['outstanding_amount'],
                'claimed_amount' => $caseData['claimed_amount'],
                'loan_amount' => $caseData['claimed_amount'] * 0.85, // Original loan amount (85% of claimed)
                'recovered_amount' => $caseData['recovered_amount'],
                'court_name' => 'Federal First Instance Court',
                'lawyer_id' => $lawyerId,
                'court_info' => 'Court location: Addis Ababa, Judge: Hon. ' . ['Almaz Teshome', 'Belay Girma', 'Chaltu Negash'][array_rand(['Almaz Teshome', 'Belay Girma', 'Chaltu Negash'])],
                'court_file_number' => 'FD/' . str_replace(['CLN-LO-', '-'], '', $caseData['file_number']) . '/2025',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // ===== 2. LABOR LITIGATION CASES =====
        $laborCases = [
            [
                'file_number' => 'LBR-LO-25001',
                'title' => 'Kidus Tadesse vs Legal Organization - Wrongful Termination',
                'description' => 'Former branch manager claims wrongful termination and demands reinstatement plus damages.',
                'claim_type' => 'Both',
                'claim_amount' => 180000,
                'claimed_amount' => 180000,
                'claim_material_desc' => 'Reinstatement to position, restoration of benefits, public apology',
                'recovered_amount' => 45000,
                'defendant' => [
                    'name' => 'Kidus Tadesse',
                    'contact_number' => '+1-555-0104',
                    'address' => 'Yeka Sub-City, Kebele 12, Addis Ababa',
                    'email' => 'kidus.tadesse@outlook.com'
                ]
            ],
            [
                'file_number' => 'LBR-LO-25002',
                'title' => 'Selamawit Degu vs Legal Organization - Overtime Payment Dispute',
                'description' => 'Customer service officer claims unpaid overtime for 2 years of extended working hours.',
                'claim_type' => 'Money', 
                'claim_amount' => 65000,
                'claimed_amount' => 65000,
                'claim_material_desc' => null,
                'recovered_amount' => 25000,
                'defendant' => [
                    'name' => 'Selamawit Degu',
                    'contact_number' => '+1-555-0105',
                    'address' => 'Bole Sub-City, Kebele 07, Addis Ababa',
                    'email' => 'selam.degu@gmail.com'
                ]
            ]
        ];

        foreach ($laborCases as $caseData) {
            $caseId = DB::table('case_files')->insertGetId([
                'file_number' => $caseData['file_number'],
                'title' => $caseData['title'],
                'description' => $caseData['description'],
                'case_type_id' => 2,
                'status' => 'Open',
                'opened_at' => Carbon::now()->subDays(rand(45, 200)),
                'branch_id' => $branchId,
                'work_unit_id' => $workUnitId,
                'court_name' => 'Labor Court of Addis Ababa',
                'created_by' => $adminId,
                'lawyer_id' => $lawyerId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Insert plaintiff (Employee/Former Employee)
            DB::table('plaintiffs')->insert([
                'case_file_id' => $caseId,
                'name' => $caseData['defendant']['name'], // Employee is plaintiff in labor case
                'contact_number' => $caseData['defendant']['contact_number'],
                'address' => $caseData['defendant']['address'],
                'email' => $caseData['defendant']['email'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Insert defendant (Legal Organization)
            DB::table('defendants')->insert([
                'case_file_id' => $caseId,
                'name' => 'Legal Organization',
                'contact_number' => '+1-555-0100',
                'address' => 'Legal Plaza, Main Building, Central District, Addis Ababa',
                'email' => 'legal@legalorg.example.com',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Insert labor litigation case details
            DB::table('labor_litigation_cases')->insert([
                'case_file_id' => $caseId,
                'claim_type' => $caseData['claim_type'],
                'claim_amount' => $caseData['claim_amount'],
                'claimed_amount' => $caseData['claimed_amount'],
                'claim_material_desc' => $caseData['claim_material_desc'],
                'recovered_amount' => $caseData['recovered_amount'],
                'outstanding_amount' => $caseData['claimed_amount'] - $caseData['recovered_amount'], // Calculate outstanding
                'loan_amount' => null, // Not applicable for labor cases
                'court_file_number' => 'LC/' . str_replace(['LBR-LO-', '-'], '', $caseData['file_number']) . '/2025',
                'early_settled' => false,
                'execution_opened_at' => null,
                'closed_at' => null,
                'closed_by' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // ===== 3. OTHER CIVIL LITIGATION CASES =====
        $civilCases = [
            [
                'file_number' => 'CIV-LO-25001',
                'title' => 'Birhanu Yilma - Breach of Service Contract',
                'description' => 'IT contractor failed to deliver promised ORGANIZATIONing software system within agreed timeframe.',
                'claim_type' => 'Both',
                'claim_amount' => 450000,
                'claimed_amount' => 450000,
                'claim_material_desc' => 'Completion of software system, source code delivery, 6-month maintenance',
                'recovered_amount' => 120000,
                'defendant' => [
                    'name' => 'Birhanu Yilma (Ethio-Tech Solutions)',
                    'contact_number' => '+1-555-0106',
                    'address' => 'CMC Area, Behind Friendship City Center, Addis Ababa',
                    'email' => 'client1@example.com'
                ]
            ],
            [
                'file_number' => 'CIV-LO-25002',
                'title' => 'Property Damage - Construction Accident',
                'description' => 'Construction company damaged ORGANIZATION building during adjacent site work. Seeking compensation.',
                'claim_type' => 'Money',
                'claim_amount' => 320000,
                'claimed_amount' => 320000,
                'claim_material_desc' => null,
                'recovered_amount' => 200000,
                'defendant' => [
                    'name' => 'Midroc Construction Company',
                    'contact_number' => '+1-555-0107',
                    'address' => 'Kazanchis Business District, Addis Ababa',
                    'email' => 'legal@company.example'
                ]
            ]
        ];

        foreach ($civilCases as $caseData) {
            $caseId = DB::table('case_files')->insertGetId([
                'file_number' => $caseData['file_number'],
                'title' => $caseData['title'],
                'description' => $caseData['description'],
                'case_type_id' => 3,
                'status' => 'Open',
                'opened_at' => Carbon::now()->subDays(rand(60, 220)),
                'branch_id' => $branchId,
                'work_unit_id' => $workUnitId,
                'court_name' => 'Federal High Court',
                'created_by' => $adminId,
                'lawyer_id' => $lawyerId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Insert plaintiff (Legal Organization)
            DB::table('plaintiffs')->insert([
                'case_file_id' => $caseId,
                'name' => 'Legal Organization',
                'contact_number' => '+1-555-0100',
                'address' => 'Legal Plaza, Main Building, Central District, Addis Ababa',
                'email' => 'legal@legalorg.example.com',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Insert defendant
            DB::table('defendants')->insert([
                'case_file_id' => $caseId,
                'name' => $caseData['defendant']['name'],
                'contact_number' => $caseData['defendant']['contact_number'],
                'address' => $caseData['defendant']['address'],
                'email' => $caseData['defendant']['email'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Insert civil litigation case details
            DB::table('other_civil_litigation_cases')->insert([
                'case_file_id' => $caseId,
                'claim_type' => $caseData['claim_type'],
                'claim_amount' => $caseData['claim_amount'],
                'claimed_amount' => $caseData['claimed_amount'],
                'claim_material_desc' => $caseData['claim_material_desc'],
                'recovered_amount' => $caseData['recovered_amount'],
                'outstanding_amount' => $caseData['claimed_amount'] - $caseData['recovered_amount'], // Calculate outstanding
                'loan_amount' => null, // Not applicable for civil litigation cases
                'court_name' => 'Federal High Court',
                'court_file_number' => 'FH/' . str_replace(['CIV-LO-', '-'], '', $caseData['file_number']) . '/2025',
                'early_settled' => false,
                'execution_opened_at' => null,
                'closed_at' => null,
                'closed_by' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // ===== 4. CRIMINAL LITIGATION CASES =====
        $criminalCases = [
            [
                'file_number' => 'CRM-LO-25001',
                'title' => 'ORGANIZATION Fraud - Forged Check Scheme',
                'description' => 'Employee and accomplice forged multiple checks totaling ETB 850,000 over 8 months.',
                'police_ref_no' => 'AA/POL/FR/2025/0089',
                'prosecutor_ref_no' => 'AAPP/FR/2025/0156',
                'evidence_summary' => 'Forged checks, CCTV footage, handwriting analysis, ORGANIZATION statements, witness testimonies',
                'status' => 'Court',
                'recovered_amount' => 320000,
                'defendant' => [
                    'name' => 'Daniel Haile & Meron Tesfaye',
                    'contact_number' => '+1-555-0108',
                    'address' => 'Nifas Silk Lafto Sub-City, Kebele 15, Addis Ababa',
                    'email' => 'daniel.haile@email.com'
                ]
            ],
            [
                'file_number' => 'CRM-LO-25002',
                'title' => 'ATM Card Fraud and Identity Theft',
                'description' => 'Organized group used skimmed ATM cards to withdraw funds from customer accounts.',
                'police_ref_no' => 'AA/POL/CYB/2025/0034',
                'prosecutor_ref_no' => 'AAPP/CYB/2025/0078',
                'evidence_summary' => 'Skimming devices, fake ATM cards, transaction logs, security camera recordings',
                'status' => 'ProsecutorReview',
                'recovered_amount' => 85000,
                'defendant' => [
                    'name' => 'Yonas Girma & Associates',
                    'contact_number' => '+1-555-0109',
                    'address' => 'Gulele Sub-City, Kebele 09, Addis Ababa',
                    'email' => 'unknown@investigation.pending'
                ]
            ]
        ];

        foreach ($criminalCases as $caseData) {
            $caseId = DB::table('case_files')->insertGetId([
                'file_number' => $caseData['file_number'],
                'title' => $caseData['title'],
                'description' => $caseData['description'],
                'case_type_id' => 4,
                'status' => 'Open',
                'opened_at' => Carbon::now()->subDays(rand(90, 300)),
                'branch_id' => $branchId,
                'work_unit_id' => $workUnitId,
                'court_name' => 'Federal Criminal Court',
                'created_by' => $adminId,
                'lawyer_id' => $lawyerId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Insert plaintiff (Legal Organization as victim/complainant)
            DB::table('plaintiffs')->insert([
                'case_file_id' => $caseId,
                'name' => 'Legal Organization',
                'contact_number' => '+1-555-0100',
                'address' => 'Legal Plaza, Main Building, Central District, Addis Ababa',
                'email' => 'legal@legalorg.example.com',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Insert defendant (accused persons)
            DB::table('defendants')->insert([
                'case_file_id' => $caseId,
                'name' => $caseData['defendant']['name'],
                'contact_number' => $caseData['defendant']['contact_number'],
                'address' => $caseData['defendant']['address'],
                'email' => $caseData['defendant']['email'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Insert criminal litigation case details
            DB::table('criminal_litigation_cases')->insert([
                'case_file_id' => $caseId,
                'police_ref_no' => $caseData['police_ref_no'],
                'prosecutor_ref_no' => $caseData['prosecutor_ref_no'],
                'police_station' => $caseData['file_number'] === 'CRM-LO-25001' ? 'Addis Ababa Federal Police Station' : 'Addis Ababa Cyber Crime Unit',
                'police_file_number' => str_replace('AA/POL/', 'POL/', $caseData['police_ref_no']),
                'evidence_summary' => $caseData['evidence_summary'],
                'status' => $caseData['status'],
                'outstanding_amount' => 850000 - $caseData['recovered_amount'], // Calculate outstanding
                'loan_amount' => null, // Not applicable for criminal cases
                'recovered_amount' => $caseData['recovered_amount'],
                'claimed_amount' => 850000, // Total fraud amount
                'court_file_number' => 'FCR/' . str_replace(['CRM-LO-', '-'], '', $caseData['file_number']) . '/2025',
                'closed_at' => null,
                'closed_by' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // ===== 5. SECURED LOAN RECOVERY CASES =====
        $securedLoanCases = [
            [
                'file_number' => 'SLR-LO-25001',
                'title' => 'Dawit Tesfaye - Mortgage Loan Recovery',
                'description' => 'Residential mortgage loan defaulted after 18 months. Property located in Bole area.',
                'loan_amount' => 2800000,
                'outstanding_amount' => 2650000,
                'claimed_amount' => 2750000,
                'collateral_description' => '3-bedroom villa with garden, Bole Sub-City, 180 sqm',
                'collateral_value' => 3200000,
                'foreclosure_notice_date' => Carbon::now()->subDays(45),
                'first_auction_held' => true,
                'second_auction_held' => false,
                'recovered_amount' => 450000,
                'customer_name' => 'Dawit Tesfaye',
                'company_file_number' => 'SLR-HOM-2023-0089',
                'collateral_estimation_path' => 'secured_loans/SLR-LO-25001/collateral_valuation_report_2025_01_15.pdf',
                'warning_doc_path' => 'secured_loans/SLR-LO-25001/foreclosure_warning_notice_2025_02_01.pdf',
                'auction_publication_path' => 'secured_loans/SLR-LO-25001/auction_publication_notice_2025_02_15.pdf'
            ],
            [
                'file_number' => 'SLR-LO-25002',
                'title' => 'Almaz Girma - Commercial Property Loan',
                'description' => 'Commercial building loan for shopping complex. Construction completed but tenant issues.',
                'loan_amount' => 5600000,
                'outstanding_amount' => 5200000,
                'claimed_amount' => 5400000,
                'collateral_description' => 'Commercial building, 4 floors, CMC area, 450 sqm',
                'collateral_value' => 6800000,
                'foreclosure_notice_date' => Carbon::now()->subDays(60),
                'first_auction_held' => false,
                'second_auction_held' => false,
                'recovered_amount' => 800000,
                'customer_name' => 'Almaz Girma',
                'company_file_number' => 'SLR-COM-2023-0156',
                'collateral_estimation_path' => 'secured_loans/SLR-LO-25002/commercial_property_valuation_2024_12_20.pdf',
                'warning_doc_path' => 'secured_loans/SLR-LO-25002/foreclosure_warning_commercial_2025_01_05.pdf',
                'auction_publication_path' => null // Not yet published since no auction held
            ],
            [
                'file_number' => 'SLR-LO-25003',
                'title' => 'Teklu Worku - Vehicle Collateral Loan',
                'description' => 'Auto loan for Toyota Land Cruiser for taxi business. Business failed due to fuel price increases.',
                'loan_amount' => 1800000,
                'outstanding_amount' => 1650000,
                'claimed_amount' => 1720000,
                'collateral_description' => '2022 Toyota Land Cruiser V8, White, Plate AA-3-12345',
                'collateral_value' => 2100000,
                'foreclosure_notice_date' => Carbon::now()->subDays(30),
                'first_auction_held' => true,
                'second_auction_held' => true,
                'recovered_amount' => 1850000,
                'customer_name' => 'Teklu Worku',
                'company_file_number' => 'SLR-VEH-2023-0234',
                'collateral_estimation_path' => 'secured_loans/SLR-LO-25003/vehicle_valuation_toyota_2025_01_10.pdf',
                'warning_doc_path' => 'secured_loans/SLR-LO-25003/vehicle_repossession_notice_2025_02_10.pdf',
                'auction_publication_path' => 'secured_loans/SLR-LO-25003/vehicle_auction_announcement_2025_02_25.pdf'
            ]
        ];

        foreach ($securedLoanCases as $caseData) {
            $caseId = DB::table('case_files')->insertGetId([
                'file_number' => $caseData['file_number'],
                'title' => $caseData['title'],
                'description' => $caseData['description'],
                'case_type_id' => 5,
                'status' => 'Open',
                'opened_at' => Carbon::now()->subDays(rand(120, 400)),
                'branch_id' => $branchId,
                'work_unit_id' => $workUnitId,
                'court_name' => 'Federal High Court - Execution Division',
                'created_by' => $adminId,
                'lawyer_id' => $lawyerId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Insert secured loan recovery case details
            DB::table('secured_loan_recovery_cases')->insert([
                'case_file_id' => $caseId,
                'loan_amount' => $caseData['loan_amount'],
                'outstanding_amount' => $caseData['outstanding_amount'],
                'claimed_amount' => $caseData['claimed_amount'],
                'collateral_description' => $caseData['collateral_description'],
                'collateral_value' => $caseData['collateral_value'],
                'foreclosure_notice_date' => $caseData['foreclosure_notice_date'],
                'first_auction_held' => $caseData['first_auction_held'],
                'second_auction_held' => $caseData['second_auction_held'],
                'recovered_amount' => $caseData['recovered_amount'],
                'customer_name' => $caseData['customer_name'],
                'company_file_number' => $caseData['company_file_number'],
                'collateral_estimation_path' => $caseData['collateral_estimation_path'] ?? null,
                'warning_doc_path' => $caseData['warning_doc_path'] ?? null,
                'auction_publication_path' => $caseData['auction_publication_path'] ?? null,
                'court_file_number' => 'FH/EX/' . str_replace(['SLR-LO-', '-'], '', $caseData['file_number']) . '/2025',
                'closure_type' => null,
                'closure_notes' => null,
                'closed_at' => null,
                'closed_by' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Add auction data if first auction held
            if ($caseData['first_auction_held']) {
                $securedLoanId = DB::table('secured_loan_recovery_cases')
                    ->where('case_file_id', $caseId)
                    ->value('id');
                    
                DB::table('secured_loan_auctions')->insert([
                    'secured_loan_recovery_case_id' => $securedLoanId,
                    'round' => 1,
                    'auction_date' => Carbon::now()->subDays(30),
                    'result' => $caseData['file_number'] === 'SLR-LO-25003' ? 'failed' : 'failed',
                    'sold_amount' => null,
                    'notes' => $caseData['file_number'] === 'SLR-LO-25003' ? 'First auction failed - no qualified bidders' : 'No qualified bidders, reserve price not met',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                
                // Add second auction data if second auction held
                if ($caseData['second_auction_held']) {
                    DB::table('secured_loan_auctions')->insert([
                        'secured_loan_recovery_case_id' => $securedLoanId,
                        'round' => 2,
                        'auction_date' => Carbon::now()->subDays(15),
                        'result' => 'sold',
                        'sold_amount' => $caseData['recovered_amount'],
                        'notes' => 'Successfully sold to qualified bidder',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }

        // ===== 6. LEGAL ADVISORY CASES =====
        $advisoryCases = [
            [
                'file_number' => 'ADV-LO-25001',
                'title' => 'Digital ORGANIZATIONing Regulation Compliance Review',
                'description' => 'Review of new NBE regulations on digital ORGANIZATIONing services and mobile money transfer limits.',
                'advisory_type' => 'written_advice',
                'subject' => 'Compliance with NBE Digital ORGANIZATIONing Directive No. DBD/2024/03',
                'requesting_department' => 'Digital ORGANIZATIONing Department',
                'work_unit_advised' => 'Technology and Innovation Unit',
                'status' => 'in_review',
                'is_own_motion' => false,
                'reference_number' => 'ADV/DBR/2025/001'
            ],
            [
                'file_number' => 'ADV-LO-25002',
                'title' => 'Anti-Money Laundering Policy Update',
                'description' => 'Legal review of updated AML policies in line with FATF recommendations and Ethiopian AML law.',
                'advisory_type' => 'document_review',
                'subject' => 'AML Policy Manual Review and Update - Version 3.0',
                'requesting_department' => 'Compliance Department',
                'work_unit_advised' => 'Risk Management Unit',
                'status' => 'completed',
                'is_own_motion' => true,
                'reference_number' => 'ADV/AML/2025/002'
            ],
            [
                'file_number' => 'ADV-LO-25003',
                'title' => 'Employment Contract Template Review',
                'description' => 'Legal review of new employment contract templates for different categories of employees.',
                'advisory_type' => 'document_review',
                'subject' => 'Standardized Employment Contract Templates - Legal Compliance',
                'requesting_department' => 'Human Resources Department',
                'work_unit_advised' => 'HR Operations Unit',
                'status' => 'approved',
                'is_own_motion' => false,
                'reference_number' => 'ADV/HR/2025/003'
            ]
        ];

        foreach ($advisoryCases as $caseData) {
            $caseId = DB::table('case_files')->insertGetId([
                'file_number' => $caseData['file_number'],
                'title' => $caseData['title'],
                'description' => $caseData['description'],
                'case_type_id' => 6,
                'status' => 'Open',
                'opened_at' => Carbon::now()->subDays(rand(15, 90)),
                'branch_id' => $branchId,
                'work_unit_id' => $workUnitId,
                'court_name' => null, // Advisory cases don't go to court
                'created_by' => $adminId,
                'lawyer_id' => $lawyerId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Legal Advisory cases typically don't have traditional plaintiffs/defendants
            // But we'll add the requesting department as plaintiff for record keeping
            DB::table('plaintiffs')->insert([
                'case_file_id' => $caseId,
                'name' => $caseData['requesting_department'],
                'contact_number' => '+1-555-0100',
                'address' => 'Legal Organization, Lancha Mekor Plaza, 3rd Floor',
                'email' => 'legal@legalorg.example.com',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Insert into legal_advisory_cases table (not legal_advisories)
            DB::table('legal_advisory_cases')->insert([
                'case_file_id' => $caseId,
                'advisory_type' => $caseData['advisory_type'],
                'subject' => $caseData['subject'],
                'description' => $caseData['description'],
                'requesting_department' => $caseData['requesting_department'],
                'work_unit_advised' => $caseData['work_unit_advised'],
                'assigned_lawyer_id' => $lawyerId,
                'request_date' => Carbon::now()->subDays(rand(15, 90)),
                'submission_date' => $caseData['status'] === 'completed' ? Carbon::now()->subDays(rand(1, 14)) : null,
                'status' => $caseData['status'],
                'is_own_motion' => $caseData['is_own_motion'],
                'reference_number' => $caseData['reference_number'],
                'approved_by' => $caseData['status'] === 'approved' ? $adminId : null,
                'approved_at' => $caseData['status'] === 'approved' ? Carbon::now()->subDays(rand(1, 7)) : null,
                'court_file_number' => null, // Advisory cases don't have court file numbers
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Also insert into the legacy legal_advisories table for backward compatibility
            DB::table('legal_advisories')->insert([
                'case_file_id' => $caseId,
                'requesting_unit' => $caseData['requesting_department'],
                'advisory_type' => $caseData['advisory_type'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}








