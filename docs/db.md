# BVS Call Reporting - Database Documentation

## Database Schema

### Table: `BVSCalls`

Primary table storing all telephone call records from the system.

| Column       | Type         | Description               |
| ------------ | ------------ | ------------------------- |
| `CallFrom` | varchar(255) | Extension making the call |
| `CallTo`   | varchar(255) | Destination number        |
| `CallTime` | datetime     | Timestamp of call         |
| `Duration` | time         | Call duration             |
| `Billing`  | time         | Billable duration         |
| `Cost`     | double(12,4) | Call cost in Rands        |
| `Status`   | varchar(50)  | Call status               |

**Engine:** InnoDB
**Charset:** latin1

### Indexes

Performance indexes for efficient queries:

- `idx_call_time_from` - Composite index on (`CallTime`, `CallFrom`)
- Optimizes date range and extension filtering

## Query Patterns

### Dashboard Queries

- Aggregate by `YEAR(CallTime)` and `MONTH(CallTime)`
- Group by month for current year
- Sum `Cost` and count records

### Monthly Summary Queries

- Filter: `CallTime` matches `{yearMonth}` pattern
- Group by: `CallFrom` (extension)
- Aggregate: COUNT(*), SUM(Cost)

### Extension Details Queries

- Filter: `CallTime` matches `{yearMonth}` AND `CallFrom` = `{extension}`
- Order by: `CallTime` DESC
- Paginated results (15 per page)

## Data Format

- Currency: South African Rand (R)
- Date format: YYYY-MMM for display
- Decimal precision: 2 places for cost display (stored as 4)

---
