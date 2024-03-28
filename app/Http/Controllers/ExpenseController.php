<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreExpenseRequest;
use App\Models\Expense;

class ExpenseController extends Controller
{
    public function index()
    {
        $expenses = Expense::orderBy('created_at')->get();
        $todayTotalIncome = 0;
        $todayTotalOutcome = 0;
        $today = date('Y-m-d');
        $todayExpenses = Expense::whereDate('date', $today)->get();

        foreach ($todayExpenses as $todayExpense) {
            if ($todayExpense->type == 'income') {
                $todayTotalIncome += $todayExpense->amount;
            } else {
                $todayTotalOutcome += $todayExpense->amount;
            }
        }

        $dayArr = [date('D')];
        $dateArr = [
            [
                'year' => date('Y'),
                'month' => date('m'),
                'day' => date('d')
            ]
        ];

        for ($i=1; $i <= 6 ; $i++) {
            $dayArr[] = date('D', strtotime("-$i day"));

            $newDate = [
                'year' => date('Y', strtotime("-$i day")),
                'month' => date('m', strtotime("-$i day")),
                'day' => date('d', strtotime("-$i day")),
            ];

            $dateArr[] = $newDate;
        }

        $dailyIncome = [];
        $dailyOutcome = [];

        foreach ($dateArr as $date) {
            $dailyIncome[] = Expense::whereYear('date', $date['year'])
                                    ->whereMonth('date', $date['month'])
                                    ->whereDay('date', $date['day'])
                                    ->where('type', 'income')
                                    ->sum('amount');

            $dailyOutcome[] = Expense::whereYear('date', $date['year'])
                                    ->whereMonth('date', $date['month'])
                                    ->whereDay('date', $date['day'])
                                    ->where('type', 'outcome')
                                    ->sum('amount');
        }

        return view('expense', compact('expenses', 'todayTotalIncome', 'todayTotalOutcome', 'dayArr', 'dailyIncome', 'dailyOutcome'));
    }

    public function store(StoreExpenseRequest $request)
    {
        $data = $request->validated();
        Expense::create($data);
        return redirect()->back()->with('success', 'Successfully Added!');
    }
}
