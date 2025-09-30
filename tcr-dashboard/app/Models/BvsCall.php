<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class BvsCall extends Model
{
    use HasFactory;

    protected $table = 'BVSCalls';

    // setting $primary_key to null, there's no primary key in the table
    protected $primaryKey = null;
    public $incrementing = false;

    protected $guarded = [];

    public $timestamps = false;

    protected $casts = [
        'CallTime' => 'datetime',
        'Cost' => 'decimal:4'
    ];

    /**
     * Get extension number from CallFrom field
     */
    public function getExtensionAttribute()
    {
        preg_match('/\((\d+)\)/', $this->CallFrom, $matches);
        return $matches[1] ?? $this->CallFrom;
    }

    /**
     * Get monthly summary for current year
     */
    public static function getMonthlySummary($year = null)
    {
        $year = $year ?? date('Y');

        $results = DB::select("
            SELECT
                DATE_FORMAT(CallTime, '%Y-%m') as month_year,
                DATE_FORMAT(CallTime, '%Y-%b') as display_month,
                MONTH(CallTime) as month_num,
                COUNT(*) as num_calls,
                SUM(Cost) as total_cost
            FROM BVSCalls
            WHERE YEAR(CallTime) = ?
            GROUP BY DATE_FORMAT(CallTime, '%Y-%m'), DATE_FORMAT(CallTime, '%Y-%b'), MONTH(CallTime)
            ORDER BY DATE_FORMAT(CallTime, '%Y-%m') ASC
        ", [$year]);

        return collect($results);
    }

    /**
     * Get all time monthly summary
     */
    public static function getAllTimeMonthlySummary()
    {
        $results = DB::select("
            SELECT
                DATE_FORMAT(CallTime, '%Y-%m') as month_year,
                DATE_FORMAT(CallTime, '%Y-%b') as display_month,
                COUNT(*) as num_calls,
                SUM(Cost) as total_cost
            FROM BVSCalls
            GROUP BY DATE_FORMAT(CallTime, '%Y-%m'), DATE_FORMAT(CallTime, '%Y-%b')
            ORDER BY DATE_FORMAT(CallTime, '%Y-%m') DESC
        ");

        return collect($results);
    }

    /**
     * Get extension summary for a specific month
     */
    public static function getExtensionSummary($yearMonth)
    {
        $results = DB::select("
            SELECT
                CallFrom,
                SUBSTRING_INDEX(SUBSTRING_INDEX(CallFrom, '(', -1), ')', 1) as extension,
                COUNT(*) as num_calls,
                SUM(Cost) as total_cost
            FROM BVSCalls
            WHERE DATE_FORMAT(CallTime, '%Y-%m') = ?
            GROUP BY CallFrom
            ORDER BY total_cost DESC
        ", [$yearMonth]);

        return collect($results);
    }

    /**
     * Get call details for specific extension and month
     */
    public static function getCallDetails($yearMonth, $extension)
    {
        // Using query builder for pagination
        return self::select('CallFrom', 'CallTo', 'CallTime', 'Duration', 'Billing', 'Cost', 'Status')
            ->whereRaw("DATE_FORMAT(CallTime, '%Y-%m') = ?", [$yearMonth])
            ->whereRaw("CallFrom LIKE ?", ["%($extension)%"])
            ->orderBy('CallTime', 'desc')
            ->paginate(50);
    }

    /**
     * Get total calls and cost for a specific year
     */
    public static function getYearSummary($year = null)
    {
        $year = $year ?? date('Y');

        $result = DB::selectOne("
            SELECT
                COUNT(*) as total_calls,
                SUM(Cost) as total_cost
            FROM BVSCalls
            WHERE YEAR(CallTime) = ?
        ", [$year]);

        return $result;
    }

    /**
     * Get top extensions by cost for a specific month or year
     */
    public static function getTopExtensions($yearMonth = null, $limit = 10)
    {
        $query = "
            SELECT
                CallFrom,
                SUBSTRING_INDEX(SUBSTRING_INDEX(CallFrom, '(', -1), ')', 1) as extension,
                COUNT(*) as num_calls,
                SUM(Cost) as total_cost,
                AVG(Cost) as avg_cost
            FROM BVSCalls
        ";

        $params = [];

        if ($yearMonth) {
            $query .= " WHERE DATE_FORMAT(CallTime, '%Y-%m') = ?";
            $params[] = $yearMonth;
        }

        $query .= "
            GROUP BY CallFrom
            ORDER BY total_cost DESC
            LIMIT ?
        ";

        $params[] = $limit;

        $results = DB::select($query, $params);

        return collect($results);
    }

    /**
     * Search calls by phone number
     */
    public static function searchByNumber($phoneNumber, $startDate = null, $endDate = null)
    {
        $query = self::select('CallFrom', 'CallTo', 'CallTime', 'Duration', 'Billing', 'Cost', 'Status')
            ->where(function($q) use ($phoneNumber) {
                $q->where('CallTo', 'LIKE', "%$phoneNumber%")
                  ->orWhere('CallFrom', 'LIKE', "%$phoneNumber%");
            });

        if ($startDate) {
            $query->where('CallTime', '>=', $startDate);
        }

        if ($endDate) {
            $query->where('CallTime', '<=', $endDate);
        }

        return $query->orderBy('CallTime', 'desc')->paginate(50);
    }

    /**
     * Get daily call statistics for a month
     */
    public static function getDailyStats($yearMonth)
    {
        $results = DB::select("
            SELECT
                DATE(CallTime) as call_date,
                COUNT(*) as num_calls,
                SUM(Cost) as total_cost,
                AVG(Cost) as avg_cost
            FROM BVSCalls
            WHERE DATE_FORMAT(CallTime, '%Y-%m') = ?
            GROUP BY DATE(CallTime)
            ORDER BY DATE(CallTime) ASC
        ", [$yearMonth]);

        return collect($results);
    }
}
