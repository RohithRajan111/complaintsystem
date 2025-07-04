<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

class Complaint extends Model
{
    
    protected $fillable = [
        'title', 'description', 'status', 'Student_id', 'Dept_id', 'attachment_path'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relationships
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'Student_id');
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Dept::class, 'Dept_id');
    }

    public function responses(): HasMany
    {
        return $this->hasMany(Complaint_Response::class, 'Complaint_id');
    }

    // Scopes for better query performance
    public function scopeByStatus(Builder $query, string $status): Builder
    {
        return $query->where('status', $status);
    }

    public function scopeByDepartment(Builder $query, int $deptId): Builder
    {
        return $query->where('Dept_id', $deptId);
    }

    public function scopeSearch(Builder $query, string $search): Builder
    {
        return $query->where(function ($q) use ($search) {
            $q->where('title', 'like', "%{$search}%")
              ->orWhere('description', 'like', "%{$search}%")
              ->orWhereHas('student', function ($q2) use ($search) {
                  $q2->where('Stud_name', 'like', "%{$search}%");
              });
        });
    }

    public function scopeOrderByPriority(Builder $query): Builder
    {
        return $query->orderByRaw("
            CASE
                WHEN status = 'pending' THEN 1
                WHEN status = 'checking' THEN 2
                WHEN status = 'solved' THEN 3
                WHEN status = 'rejected' THEN 4
                WHEN status = 'withdrawn' THEN 5
                ELSE 6
            END
        ");
    }

    // Efficient pagination method using cursor-based approach
    public static function paginateEfficiently($perPage = 10, $lastId = null, $filters = [])
    {
        $query = self::with(['student:id,Stud_name,Stud_email', 'department:id,Dept_name']);

        // Apply filters
        if (!empty($filters['search'])) {
            $query->search($filters['search']);
        }
        if (!empty($filters['status']) && $filters['status'] !== 'all') {
            $query->byStatus($filters['status']);
        }
        if (!empty($filters['dept'])) {
            $query->byDepartment($filters['dept']);
        }

        // Cursor-based pagination for better performance
        if ($lastId) {
            $query->where('id', '<', $lastId);
        }

        $query->orderBy('id', 'desc');
        $results = $query->take($perPage + 1)->get();

        $hasMore = $results->count() > $perPage;
        if ($hasMore) {
            $results->pop(); // Remove the extra record
        }

        return [
            'data' => $results,
            'has_more' => $hasMore,
            'last_id' => $results->last()?->id,
        ];
    }

    // Get counts efficiently with caching
    public static function getStatusCounts()
    {
        $cacheKey = 'complaint_counts_' . date('Y-m-d-H');
        
        return cache()->remember($cacheKey, 3600, function () {
            // Use raw queries for better performance
            $counts = self::selectRaw('
                COUNT(*) as total,
                SUM(CASE WHEN status = "pending" THEN 1 ELSE 0 END) as pending,
                SUM(CASE WHEN status = "checking" THEN 1 ELSE 0 END) as checking,
                SUM(CASE WHEN status = "solved" THEN 1 ELSE 0 END) as solved,
                SUM(CASE WHEN status = "rejected" THEN 1 ELSE 0 END) as rejected,
                SUM(CASE WHEN status = "withdrawn" THEN 1 ELSE 0 END) as withdrawn
            ')->first();

            return [
                'total' => $counts->total,
                'pending' => $counts->pending,
                'checking' => $counts->checking,
                'solved' => $counts->solved,
                'rejected' => $counts->rejected,
                'withdrawn' => $counts->withdrawn,
            ];
        });
    }

    // Optimize the search for large datasets
    public static function searchOptimized($search, $perPage = 10, $page = 1)
    {
        $query = self::with(['student:id,Stud_name,Stud_email', 'department:id,Dept_name']);

        if (!empty($search)) {
            // Use MATCH AGAINST for full-text search if available
            $query->whereRaw("MATCH(title) AGAINST(? IN BOOLEAN MODE)", [$search])
                  ->orWhere('title', 'like', "%{$search}%")
                  ->orWhereHas('student', function ($q) use ($search) {
                      $q->where('Stud_name', 'like', "%{$search}%");
                  });
        }

        return $query->orderBy('id', 'desc')->paginate($perPage, ['*'], 'page', $page);
    }

    // Chunk processing for large operations
    public static function processInChunks($callback, $chunkSize = 1000)
    {
        self::chunk($chunkSize, $callback);
    }
}