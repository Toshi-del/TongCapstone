# Annual Physical Tab Filtering Fix

## Issues Fixed

### 1. **Examination Type Mismatch** 
- **Problem**: Controller was filtering for `'annual-physical'` (hyphen) but database stores `'annual_physical'` (underscore)
- **Solution**: Updated all references to use `'annual_physical'` consistently

### 2. **Status Workflow Bypass**
- **Problem**: Pathologist could set status to `'sent_to_company'`, bypassing doctor review
- **Solution**: 
  - Restricted pathologist to only set status as `'Pending'`
  - Added validation to only accept `'Pending'` status
  - Force status to always be `'Pending'` in controller
  - Updated UI to show disabled dropdown with info message

### 3. **Field Name Mismatch in Lab Report Filtering**
- **Problem**: 
  - Filtering logic checked for hardcoded fields like `cbc_result`, `urinalysis_result`
  - Actual data has dynamic field names like `chloride`, `inor._phosp`, `hba1c`, etc.
  - This caused records to appear in both tabs or neither tab
- **Solution**: 
  - Changed filtering to use `JSON_SEARCH()` to look for actual result values (`'Normal'`, `'Not Normal'`, etc.)
  - This works regardless of field names
  - **Needs Review**: Records with NO result values (Normal/Abnormal/etc.)
  - **Lab Completed**: Records WITH result values (Normal/Abnormal/etc.)

## Changes Made

### PathologistController.php

1. **Line 211, 258, 386**: Changed `'annual-physical'` → `'annual_physical'`

2. **Line 986**: Validation changed from `'in:Pending,completed,sent_to_company'` → `'in:Pending'`

3. **Line 1014-1018**: Force status to always be `'Pending'` with comment

4. **Lines 206-243**: Updated `needs_review` filtering logic:
   - Removed hardcoded field name checks
   - Added `JSON_SEARCH()` to check for absence of result values

5. **Lines 255-282**: Updated `lab_completed` filtering logic:
   - Removed hardcoded field name checks
   - Added `JSON_SEARCH()` to check for presence of result values

### annual-physical.blade.php

1. **Lines 61, 109, 369**: Changed `'annual-physical'` → `'annual_physical'`

2. **Lines 52-97**: Updated "Needs Review" tab count query to match controller logic

3. **Lines 102-134**: Updated "Lab Completed" tab count query to match controller logic

### annual-physical-edit.blade.php

1. **Lines 370-400**: 
   - Removed status dropdown options (completed, sent_to_company)
   - Made dropdown disabled with only "Pending" option
   - Added hidden input to ensure "Pending" is submitted
   - Added blue info box explaining pathologist workflow
   - Changed button text from "Update Examination" to "Save Lab Results"

## Workflow

1. **Phlebotomist** completes blood extraction → Medical checklist saved
2. **Pathologist** enters lab results → Status: `'Pending'` → Appears in "Lab Completed" tab
3. **Doctor** reviews and submits → Status: `'sent_to_company'`
4. **Admin** processes → Status: `'Approved'`

## Testing

Run `php test_tabs_separation.php` to verify:
- Patients appear in correct tab
- No overlap between tabs
- Counts are accurate

## Result

✓ Tabs are now properly separated
✓ Completed examinations appear only in "Lab Completed" tab
✓ Workflow enforces proper review chain
