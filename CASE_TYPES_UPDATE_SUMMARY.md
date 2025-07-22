# Case Types Database Update Summary

This document summarizes the comprehensive updates made to all case type tables in the Legal Case Management System on July 19, 2025.

## Overview

Updated all 6 case types to ensure consistency across the database schema and models:

1. **Clean Loan Recovery** (Code: 01)
2. **Labor Litigation** (Code: 02)
3. **Other Civil Litigation** (Code: 03)
4. **Criminal Litigation** (Code: 04)
5. **Secured Loan Recovery** (Code: 05)
6. **Legal Advisory** (Code: 06)

## Database Changes Made

### Migration: `2025_07_19_140845_update_all_case_type_tables.php`

#### Clean Loan Recovery Cases Table
**New Columns Added:**
- `branch_id` (foreign key to branches)
- `work_unit_id` (foreign key to work_units)
- `company_file_number`
- `customer_name`
- `loan_amount`
- `court_name`
- `lawyer_id` (foreign key to users)
- `court_info`

#### Labor Litigation Cases Table
**New Columns Added:**
- `outstanding_amount` (decimal 15,2, default 0)
- `loan_amount` (decimal 15,2, nullable)

#### Other Civil Litigation Cases Table
**New Columns Added:**
- `outstanding_amount` (decimal 15,2, default 0)
- `loan_amount` (decimal 15,2, nullable)
- `court_name`

#### Criminal Litigation Cases Table
**New Columns Added:**
- `outstanding_amount` (decimal 15,2, default 0)
- `loan_amount` (decimal 15,2, nullable)
- `police_station`
- `police_file_number`

#### Secured Loan Recovery Cases Table
**New Columns Added:**
- `work_unit_id` (foreign key to work_units)
- `branch_id` (foreign key to branches)

#### Legal Advisory Cases Table
**New Columns Added:**
- `outstanding_amount` (decimal 15,2, default 0)
- `recovered_amount` (decimal 15,2, default 0)
- `claimed_amount` (decimal 15,2, default 0)
- `loan_amount` (decimal 15,2, nullable)

#### Litigation Cases Table (Legacy)
**New Columns Added:**
- `court_file_number`
- `loan_amount` (decimal 15,2, nullable)

## Model Updates

Updated all case type models to include the new fillable attributes and proper casting:

### 1. CleanLoanRecoveryCase Model
```php
protected $fillable = [
    'case_file_id', 'branch_id', 'work_unit_id', 'company_file_number',
    'customer_name', 'outstanding_amount', 'claimed_amount', 'recovered_amount',
    'loan_amount', 'court_name', 'lawyer_id', 'court_info', 'court_file_number'
];
```

### 2. LaborLitigationCase Model
```php
protected $fillable = [
    'case_file_id', 'claim_type', 'claim_amount', 'claimed_amount',
    'claim_material_desc', 'recovered_amount', 'outstanding_amount', 'loan_amount',
    'early_settled', 'execution_opened_at', 'closed_at', 'closed_by', 'court_file_number'
];
```

### 3. OtherCivilLitigationCase Model
```php
protected $fillable = [
    'case_file_id', 'claim_type', 'claim_amount', 'claim_material_desc',
    'claimed_amount', 'recovered_amount', 'outstanding_amount', 'loan_amount',
    'court_name', 'early_settled', 'execution_opened_at', 'closed_at',
    'closed_by', 'court_file_number'
];
```

### 4. CriminalLitigationCase Model
```php
protected $fillable = [
    'case_file_id', 'police_ref_no', 'prosecutor_ref_no', 'police_station',
    'police_file_number', 'evidence_summary', 'status', 'outstanding_amount',
    'loan_amount', 'claimed_amount', 'recovered_amount', 'closed_at',
    'closed_by', 'court_file_number'
];
```

### 5. LegalAdvisoryCase Model
```php
protected $fillable = [
    'case_file_id', 'advisory_type', 'subject', 'description',
    'assigned_lawyer_id', 'request_date', 'submission_date', 'status',
    'outstanding_amount', 'recovered_amount', 'claimed_amount', 'loan_amount',
    'document_path', 'review_notes', 'reviewed_document_path', 'is_own_motion',
    'reference_number', 'approved_by', 'approved_at', 'closure_notes',
    'closed_at', 'closed_by', 'court_file_number'
];
```

## Controller Updates

Updated the case controller methods to handle the new fields:

### Updated Methods:
1. **handleCivilData()** - Now includes claim_type, claim_amount, claim_material_desc, and other civil litigation specific fields
2. **handleCriminalData()** - Now includes police_ref_no, prosecutor_ref_no, police_station, police_file_number, evidence_summary, and criminal status fields
3. **handleCleanLoanData()** - Already comprehensive, includes all new fields
4. **handleLaborData()** - Already comprehensive
5. **handleSecuredLoanData()** - Already comprehensive
6. **handleAdvisoryData()** - Already comprehensive

## Data Casting

All models now include proper decimal casting for financial fields:
```php
protected $casts = [
    'outstanding_amount'  => 'decimal:2',
    'loan_amount'         => 'decimal:2',
    'claimed_amount'      => 'decimal:2',
    'recovered_amount'    => 'decimal:2',
];
```

## Benefits of These Updates

1. **Consistency**: All case type tables now have standardized financial columns
2. **Flexibility**: Each case type can track specific amounts relevant to their context
3. **Reporting**: Enhanced reporting capabilities with consistent data structure
4. **Integrity**: Foreign key relationships ensure data consistency
5. **Extensibility**: Easy to add new features that require financial tracking

## Backward Compatibility

- All existing data is preserved
- New columns are nullable or have default values
- No breaking changes to existing functionality
- Migration can be rolled back if needed

## Next Steps

1. Update frontend forms to include new fields where appropriate
2. Update validation rules in controllers
3. Update reports to utilize new standardized fields
4. Test all case type functionality
5. Update user documentation

## Migration Command Used

```bash
php artisan migrate --path=database/migrations/2025_07_19_140845_update_all_case_type_tables.php
```

## Rollback (If Needed)

```bash
php artisan migrate:rollback --step=1
```

---

**Date**: July 19, 2025  
**Status**: Complete  
**Migration Status**: Successfully executed  
**Models Updated**: 6 case type models  
**Controllers Updated**: CaseController methods






