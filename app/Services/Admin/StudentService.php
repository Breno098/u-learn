<?php

namespace App\Services\Admin;

use App\Models\Student;
use Illuminate\Support\Arr;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;

class StudentService
{
    /**
     * @param array $filters
     * @param null|integer $rowsPerPage
     * @param null|string $orderBy
     * @param null|string $sort
     * @return Student[]|Collection|LengthAwarePaginator
     */
    public function index(
        array $filters = [],
        null|int $rowsPerPage = 10,
        null|string $orderBy = 'id',
        null|string $sort = 'asc'
    ): LengthAwarePaginator|Collection
    {
         /** @var Student|Builder */
        $query = Student::query()->select(['students.*']);

        if ($name = Arr::get($filters, 'name')) {
            $query->where('name', 'like', "%{$name}%");
        }

        if ($email = Arr::get($filters, 'email')) {
            $query->where('email', $email);
        }

        if ($cpf = Arr::get($filters, 'cpf')) {
            $query->where('cpf', $cpf);
        }

        if ($phone = Arr::get($filters, 'phone')) {
            $query->where('phone', $phone);
        }

        if (in_array($orderBy, (new Student)->getFillable())) {
            $query->orderBy("students.{$orderBy}", $sort);
        }

        return $query->paginate($rowsPerPage);
    }

    /**
     * @param array $requestData
     * @return Student
     */
    public function store(array $requestData = []): Student
    {
        $requestData['password'] = Str::random(20);

        $student = Student::create($requestData);

        $student->groups()->sync(Arr::get($requestData, 'group_ids'));

        return $student;
    }

    /**
     * @param Student $student
     * @param array $requestData
     * @return Student
     */
    public function update(Student $student, array $requestData = []): Student
    {
        $student->update($requestData);

        $student->groups()->sync(Arr::get($requestData, 'group_ids'));

        return $student;
    }

    /**
     * @param Student $student
     * @return boolean|null
     */
    public function delete(Student $student): ?bool
    {
        $student->groups()->detach();

        return $student->delete();
    }

    /**
     * @param array $ids
     * @return void
     */
    public function deleteMultiple(array $ids = []): void
    {
        foreach ($ids as $id) {
            if ($student = Student::find($id)) {
                $this->delete($student);
            }
        }
    }

    /**
     * @param Student $student
     * @return Student
     */
    public function activate(Student $student): Student
    {
        $student->update(['active' => true]);

        return $student;
    }

    /**
     * @param Student $student
     * @return Student
     */
    public function inactivate(Student $student): Student
    {
        $student->update(['active' => false]);

        return $student;
    }

     /**
     * @param integer $month
     * @param integer|null|null $year
     * @return float
     */
    public function createdByMonthCount(int $month, int|null $year = null): float
    {
        $date = Carbon::create($year ?? now()->format('Y'), $month, 1);

        return Student::whereMonth('created_at', $date->month)
            ->whereYear('created_at', $date->year)
            ->count();
    }
}
