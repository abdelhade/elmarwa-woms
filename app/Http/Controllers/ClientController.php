<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Contracts\View\View;
use App\Imports\ClientImport;
use Maatwebsite\Excel\Facades\Excel;

class ClientController extends Controller
{
    public function index(Request $request): View
    {
        $query = Client::query();

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%')
                  ->orWhere('phone', 'like', '%' . $request->search . '%');
            });
        }

        $clients = $query->orderBy('sort_order')->orderByDesc('created_at')->get();

        return view('clients.index', compact('clients'));
    }

    public function create(): View
    {
        return view('clients.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:clients,email',
            'phone' => 'nullable|string|max:50',
            'address' => 'nullable|string',
            'credit_limit' => 'nullable|numeric|min:0',
            'sort_order' => 'nullable|integer|min:1',
        ]);

        Client::create($data);

        return redirect()->route('clients.create')
            ->with('success', 'تم إنشاء العميل بنجاح.');
    }

    public function show(Client $client): View
    {
        $client->load(['workOrders' => function ($q) {
            $q->with('product')->orderByDesc('created_at')->limit(10);
        }]);
        return view('clients.show', compact('client'));
    }

    public function edit(Client $client): View
    {
        return view('clients.edit', compact('client'));
    }

    public function update(Request $request, Client $client): RedirectResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:clients,email,' . $client->id,
            'phone' => 'nullable|string|max:50',
            'address' => 'nullable|string',
            'credit_limit' => 'nullable|numeric|min:0',
            'sort_order' => 'nullable|integer|min:1',
        ]);

        $client->update($data);

        return redirect()->route('clients.show', $client)
            ->with('success', 'تم تحديث بيانات العميل بنجاح.');
    }

    public function destroy(Client $client): RedirectResponse
    {
        if ($client->workOrders()->count() > 0) {
            return back()->with('error', 'لا يمكن حذف عميل مرتبط بأوامر عمل.');
        }

        $client->delete();

        return redirect()->route('clients.index')
            ->with('success', 'تم حذف العميل بنجاح.');
    }

    public function bulkDestroy(Request $request): RedirectResponse
    {
        $request->validate([
            'ids'   => 'required|array|min:1',
            'ids.*' => 'integer|exists:clients,id',
        ]);

        $ids = $request->ids;

        // Skip clients that have work orders
        $skipped = Client::whereIn('id', $ids)->whereHas('workOrders')->count();
        $deleted = Client::whereIn('id', $ids)->whereDoesntHave('workOrders')->delete();

        $msg = "تم حذف {$deleted} عميل بنجاح.";
        if ($skipped > 0) {
            $msg .= " تم تخطي {$skipped} عميل مرتبط بأوامر عمل.";
        }

        return redirect()->route('clients.index')->with('success', $msg);
    }

    public function import(Request $request): RedirectResponse
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:2048',
        ]);

        try {
            Excel::import(new ClientImport, $request->file('file'));
            return back()->with('success', 'تم استيراد العملاء بنجاح.');
        } catch (\Exception $e) {
            return back()->with('error', 'حدث خطأ أثناء الاستيراد: ' . $e->getMessage());
        }
    }

    public function exportTemplate()
    {
        $headers = ['name', 'email', 'phone', 'address', 'company', 'tax_number', 'credit_limit'];
        $data = [
            ['Ahmed Mohamed', 'ahmed@example.com', '0123456789', 'Cairo, Egypt', 'Tech Co', '123-456', '5000'],
        ];

        return response()->streamDownload(function () use ($headers, $data) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $headers);
            foreach ($data as $row) {
                fputcsv($file, $row);
            }
            fclose($file);
        }, 'clients_template.csv');
    }

    public function updateSort(Request $request, Client $client): \Illuminate\Http\JsonResponse
    {
        $data = $request->validate([
            'sort_order' => 'nullable|integer|min:1'
        ]);

        $client->update(['sort_order' => $data['sort_order']]);

        return response()->json([
            'success' => true,
            'message' => 'تم تحديث الترتيب بنجاح'
        ]);
    }

    public function updateSortBatch(Request $request): \Illuminate\Http\JsonResponse
    {
        $request->validate([
            'order' => 'required|array',
            'order.*' => 'integer|exists:clients,id',
            'start_index' => 'required|integer|min:1',
        ]);

        $currentIndex = $request->start_index;
        foreach ($request->order as $id) {
            Client::where('id', $id)->update(['sort_order' => $currentIndex]);
            $currentIndex++;
        }

        return response()->json([
            'success' => true,
            'message' => 'تم حفظ الترتيب بنجاح'
        ]);
    }
}
