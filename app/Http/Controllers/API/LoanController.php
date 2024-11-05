<?php

namespace App\Http\Controllers\API;

use App\Models\Loan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Validation\ValidationException;

class LoanController extends Controller
{
    /**
     * Display a listing of all loans.
     * 
     * This endpoint is publicly accessible and allows any user to view all loans.
     * 
     * @return \Illuminate\Http\JsonResponse JSON response containing all loans.
     */
    public function index()
    {
        $loans = Loan::with('lender', 'borrower')->get();

        return response()->json([
            'status' => 'success',
            'message' => 'Loans retrieved successfully.',
            'data' => $loans
        ], 200);
    }

    /**
     * Display a specific loan by ID.
     * 
     * This endpoint is publicly accessible and allows any user to view details of a specific loan.
     * 
     * @param int $id The ID of the loan to retrieve.
     * @return \Illuminate\Http\JsonResponse JSON response containing loan details or error if not found.
     */
    public function show($id)
    {
        $loan = Loan::with('lender', 'borrower')->find($id);

        if ($loan) {
            return response()->json([
                'status' => 'success',
                'message' => 'Loan retrieved successfully.',
                'data' => $loan
            ], 200);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'Loan not found.',
            'data' => null
        ], 404);
    }

    /**
     * Store a newly created loan.
     * 
     * This endpoint is only accessible to authenticated users (typically lenders).
     * The loan is associated with the authenticated lender and a specified borrower.
     * 
     * @param \Illuminate\Http\Request $request The request containing loan details.
     * @return \Illuminate\Http\JsonResponse JSON response containing the created loan or validation errors.
     */
    public function store(Request $request)
    {
        try {
            // Validate request data and ensure lender and borrower are not the same
            $request->validate([
                'amount' => 'required|numeric|min:0',
                'interest_rate' => 'required|numeric|min:0',
                'duration_years' => 'required|integer|min:1',
                'borrower_id' => 'required|exists:users,id',
            ]);

            // Ensure lender and borrower are different
            if (Auth::id() == $request->borrower_id) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'The lender and borrower cannot be the same user.',
                    'data' => null
                ], 422);
            }

        } catch (ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed. Please check the input fields.',
                'data' => $e->errors()
            ], 422);
        }

        // Create the loan
        $loan = Loan::create([
            'amount' => $request->amount,
            'interest_rate' => $request->interest_rate,
            'duration_years' => $request->duration_years,
            'lender_id' => Auth::id(), // Authenticated user is the lender
            'borrower_id' => $request->borrower_id
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Loan created successfully.',
            'data' => $loan
        ], 201);
    }


    /**
     * Update an existing loan.
     * 
     * Only the original lender (authenticated user) can update the loan.
     * 
     * @param \Illuminate\Http\Request $request The request containing updated loan details.
     * @param int $id The ID of the loan to update.
     * @return \Illuminate\Http\JsonResponse JSON response containing the updated loan or error if unauthorized/not found.
     */
    public function update(Request $request, $id)
    {
        $loan = Loan::find($id);

        if (!$loan) {
            return response()->json([
                'status' => 'error',
                'message' => 'Loan not found. Unable to update non-existing loan.',
                'data' => null
            ], 404);
        }

        // Check if the authenticated user is the original lender
        if (Auth::id() !== $loan->lender_id) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized. Only the original lender can update this loan.',
                'data' => null
            ], 403);
        }

        try {
            // Validate request data
            $request->validate([
                'amount' => 'sometimes|numeric|min:0',
                'interest_rate' => 'sometimes|numeric|min:0',
                'duration_years' => 'sometimes|integer|min:1',
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed. Please check the input fields.',
                'data' => $e->errors()
            ], 422);
        }

        // Update the loan fields
        $loan->amount = $request->amount ?? $loan->amount;
        $loan->interest_rate = $request->interest_rate ?? $loan->interest_rate;
        $loan->duration_years = $request->duration_years ?? $loan->duration_years;
        $loan->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Loan updated successfully.',
            'data' => $loan
        ], 200);
    }

    /**
     * Remove an existing loan.
     * 
     * Only the original lender (authenticated user) can delete the loan.
     * 
     * @param int $id The ID of the loan to delete.
     * @return \Illuminate\Http\JsonResponse JSON response confirming deletion or error if unauthorized/not found.
     */
    public function destroy($id)
    {
        $loan = Loan::find($id);

        if (!$loan) {
            return response()->json([
                'status' => 'error',
                'message' => 'Loan not found. Unable to delete non-existing loan.',
                'data' => null
            ], 404);
        }

        // Check if the authenticated user is the original lender
        if (Auth::id() !== $loan->lender_id) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized. Only the original lender can delete this loan.',
                'data' => null
            ], 403);
        }

        $loan->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Loan deleted successfully.',
            'data' => null
        ], 200);
    }
}
