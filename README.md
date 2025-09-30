# BVS Call Reporting Dashboard

A Laravel telephone call reporting system with interactive drill-down capabilities and ECharts visualization.

## Overview

Three-level drill-down dashboard for call analytics:

- **Dashboard** → Year overview with monthly aggregates
- **Monthly Summary** → Extension-level breakdown for selected month
- **Extension Details** → Individual call records with pagination

## Requirements Met

- **Single Table** - All pages query `BVSCalls` table with date/extension filters  
- **Filtering & Pagination** - Month/extension filters with Laravel pagination on all tables  
- **Sortable Tables** - Client-side sorting on all columns with visual indicators  
- **Responsive Charts** - ECharts dual-axis bar graph with window resize handling  
- **Consistent Formatting** - YYYY-MMM date format, R currency with 2 decimals throughout  
- **Reliable Routing** - Laravel named routes with `{yearMonth}` and `{extension}` parameters  
- **Bootstrap Theme** - Bootstrap 5 styling with card layouts and breadcrumb navigation

## 3 Minute Demo Video

Watch a quick demo of the dashboard in action here: [Demo Video](docs/demo/demo.mp4)

## Quick Start

```bash
composer install
cp .env.example .env
php artisan key:generate
# Configure database in .env
php artisan migrate
php artisan serve
```

## Tech Stack

- Laravel 10.x / PHP 8.1+
- Bootstrap 5 + Bootstrap Icons
- ECharts 5.x
- MySQL 5.7+

## Routes

- `/` - Dashboard (current year graph + historical table)
- `/calls/month/{yearMonth}` - Monthly summary by extension
- `/calls/month/{yearMonth}/extension/{extension}` - Call details with pagination

## Database

**Table:** `BVSCalls`  
**Key Columns:** `CallFrom`, `CallTo`, `CallTime`, `Duration`, `Cost`  
**Indexes:** Composite indexes on `CallTime` + `CallFrom` for performance

## License

MIT License - see [LICENSE](LICENSE) for details.

---