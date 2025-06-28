<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Submission extends Model
{
    use HasFactory;

    protected $fillable = [
        'assignment_id',
        'student_id',
        'content',
        'file_path',
        'file_name',
        'submitted_at',
        'grade',
        'feedback',
        'graded_at',
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
        'graded_at' => 'datetime',
    ];

    // Relationships
    public function assignment()
    {
        return $this->belongsTo(Assignment::class);
    }

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    // Helper methods
    public function isGraded()
    {
        return !is_null($this->grade);
    }

    public function isLate()
    {
        return $this->submitted_at > $this->assignment->due_date;
    }

    public function getScorePercentage()
    {
        if (!$this->isGraded() || $this->assignment->points == 0) {
            return null;
        }
        return round(($this->grade / $this->assignment->points) * 100, 2);
    }

    public function getLetterGrade()
    {
        $percentage = $this->getScorePercentage();
        if ($percentage === null) return 'Not Graded';

        if ($percentage >= 90) return 'A';
        if ($percentage >= 80) return 'B';
        if ($percentage >= 70) return 'C';
        if ($percentage >= 60) return 'D';
        return 'F';
    }

    // Scopes
    public function scopeGraded($query)
    {
        return $query->whereNotNull('grade');
    }

    public function scopeUngraded($query)
    {
        return $query->whereNull('grade');
    }

    public function scopeForAssignment($query, $assignmentId)
    {
        return $query->where('assignment_id', $assignmentId);
    }

    public function scopeForStudent($query, $studentId)
    {
        return $query->where('student_id', $studentId);
    }
}
