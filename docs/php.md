# BVS Call Reporting - Coding Specification

## Project Overview

Thi is a three-level drill-down telephone call reporting dashboard using Laravel, Bootstrap 5, and ECharts, querying a single `BVSCalls` table.

---

## Page Specifications

### Page 1: Dashboard (`/`)

**Route:** `GET /`  
**Controller:** `CallReportController@dashboard`

#### A. Bar Graph (ECharts)

- **Data Source:** Current year aggregated by month
- **X-Axis:** Month names (Jan - Current Month)
- **Y-Axis (Dual):**
  - Left: Number of calls (blue bars)
  - Right: Total cost in Rands (green bars)
- **Query:** `WHERE YEAR(CallTime) = {currentYear} GROUP BY MONTH(CallTime)`
- **Must be responsive** (resize listener implemented)

#### B. Historical Data Table

**Columns:**

1. **Year-Month** (YYYY-MMM format) - Hyperlink to Monthly Summary
2. Number of Calls - Formatted with thousand separators
3. Total Cost - R #,###.## format

**Features:**

- Client-side column sorting (all columns)
- Sort icons with visual feedback
- Query: All historical data `GROUP BY YEAR(CallTime), MONTH(CallTime)`

---

### Page 2: Monthly Summary (`/calls/month/{yearMonth}`)

**Route:** `GET /calls/month/{yearMonth}`  
**Controller:** `CallReportController@monthlySummary`  
**Parameters:** `{yearMonth}` in YYYY-MM format

#### Summary Cards

- Total Calls for month
- Total Cost for month

#### Extension Breakdown Table

**Columns:**

1. **Extension** (`CallFrom`) - Hyperlink to Extension Details
2. Number of Calls - Per extension for selected month
3. Total Cost - Per extension, R #,###.## format

**Features:**

- Breadcrumb navigation (Dashboard → Current Month)
- Client-side sorting on all columns
- Query: `WHERE DATE_FORMAT(CallTime, '%Y-%m') = {yearMonth} GROUP BY CallFrom`

---

### Page 3: Extension Details (`/calls/month/{yearMonth}/extension/{extension}`)

**Route:** `GET /calls/month/{yearMonth}/extension/{extension}`  
**Controller:** `CallReportController@extensionDetails`  
**Parameters:**

- `{yearMonth}` - YYYY-MM format
- `{extension}` - Extension number

---
