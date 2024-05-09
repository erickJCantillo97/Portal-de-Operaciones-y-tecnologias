<?php

namespace App\Http\Controllers\Projects;

use App\Http\Controllers\Controller;
use App\Models\Projects\Customer;
use Exception;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CustomerController extends Controller
{

    public function __construct()
    {
        $this->authorizeResource(Customer::class, 'customer');
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Customer $customers)
    {
        $customers = Customer::orderBy('name')->get();

        return Inertia::render('Project/Customers/Index', compact('customers'));
        // return response()->json([
        //     $customer,
        // ], 200);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // return Inertia::render('Project/CustomersForm');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // dd($request);
        $validateData = $request->validate([
            'NIT' => 'nullable',
            'name' => 'required',
            'country' => 'nullable',
            'country_en' => 'nullable',
            'type' => 'nullable',
            'email' => 'nullable|email',
        ]);

        try {
            Customer::create($validateData);

            return back()->with(['message' => 'Cliente creado correctamente'], 200);
        } catch (Exception $e) {
            return back()->withErrors(['message' => 'Ocurrió un error al crear el cliente: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(customer $customer)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Customer $customer)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Customer $customer)
    {
        $validateData = $request->validate([
            'NIT' => 'nullable',
            'name' => 'required',
            'country' => 'nullable',
            'country_en' => 'nullable',
            'type' => 'required',
            'email' => 'nullable|email',
        ]);

        try {
            $customer->update($validateData);
        } catch (Exception $e) {
            return back()->withErrors('message', 'Ocurrio un Error Al Actualizar : ' . $e);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Customer $customer)
    {
        try {
            if ($customer->contract->count() == 0) {
                $customer->delete();
                return back()->with(['message' => 'Cliente eliminado']);
            }
            return back()->withErrors(['message' =>  'No se Puede Eliminar un Cliente con Contratos']);
        } catch (Exception $e) {
            return back()->with('message', 'Ocurrio un error el eliminar : ' . $e);
        }

        return redirect('customers.index');
    }
}
