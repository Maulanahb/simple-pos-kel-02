<?php

namespace App\Http\Controllers;

use App\Exports\SalesReportExport;
use App\Jobs\ProcessProductImport;
use App\Models\Category;
use App\Models\ImportError;
use App\Models\Transaction;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index(Request $request): View
    {
        [$from, $to] = $this->resolveDateRange($request);
        $categoryId = $this->resolveCategoryId($request);
        $query = $this->filteredQuery($from, $to, $categoryId);

        return view('reports.index', [
            'totalPenjualan' => $query->sum('total'),
            'jumlahTransaksi' => $query->count(),
            'from' => $from->toDateString(),
            'to' => $to->toDateString(),
            'categories' => Category::orderBy('name')->get(),
            'categoryId' => $categoryId,
        ]);
    }

    public function exportCsv(Request $request): StreamedResponse
    {
        [$from, $to] = $this->resolveDateRange($request);
        $categoryId = $this->resolveCategoryId($request);
        $query = $this->filteredQuery($from, $to, $categoryId);

        return response()->streamDownload(function () use ($query) {
            $out = fopen('php://output', 'w');

            fputcsv($out, ['ID', 'Tanggal', 'Kasir', 'Total']);

            foreach ($query->lazyById() as $transaction) {
                fputcsv($out, [
                    $transaction->id,
                    $transaction->created_at,
                    $transaction->user->name ?? '-',
                    $transaction->total,
                ]);
            }

            fclose($out);
        }, 'laporan-penjualan.csv');
    }

    public function exportExcel(Request $request): BinaryFileResponse
    {
        [$from, $to] = $this->resolveDateRange($request);
        $categoryId = $this->resolveCategoryId($request);

        return Excel::download(
            new SalesReportExport(
                $this->filteredQuery($from, $to, $categoryId)
            ),
            'laporan-penjualan.xlsx'
        );
    }

    public function exportPdf(Request $request): Response
    {
        [$from, $to] = $this->resolveDateRange($request);
        $categoryId = $this->resolveCategoryId($request);
        $query = $this->filteredQuery($from, $to, $categoryId);
        $produkTerlaris = DB::table('transaction_details')
            ->join('products', 'transaction_details.product_id', '=', 'products.id')
            ->join('transactions', 'transaction_details.transaction_id', '=', 'transactions.id')
            ->whereBetween('transactions.created_at', [$from, $to])
            ->when($categoryId, function ($q) use ($categoryId) {
                $q->where('products.category_id', $categoryId);
            })
            ->select(
                'products.name',
                DB::raw('SUM(transaction_details.qty) as total_terjual'),
                DB::raw('SUM(transaction_details.subtotal) as total_pendapatan')
            )
            ->groupBy('products.id', 'products.name')
            ->orderByDesc('total_terjual')
            ->limit(10)
            ->get();

        $pdf = Pdf::loadView('reports.pdf', [
            'totalPenjualan' => $query->sum('total'),
            'jumlahTransaksi' => $query->count(),
            'produkTerlaris' => $produkTerlaris,
            'category' => $categoryId ? Category::find($categoryId) : null,
            'transactions' => $this->filteredQuery(
                $from,
                $to,
                $categoryId
            )->latest()->limit(50)->get(),
            'from' => $from->toDateString(),
            'to' => $to->toDateString(),
        ]);

        return $pdf->download('laporan-penjualan.pdf');
    }

    public function importForm(): View
    {
        return view('reports.import', [
            'importErrors' => ImportError::latest()->paginate(10),
        ]);
    }

    public function clearImportErrors(): RedirectResponse
    {
        ImportError::truncate();

        return back()->with('success', 'Riwayat error import berhasil dibersihkan.');
    }

    public function import(Request $request): RedirectResponse
    {
        $request->validate([
            'csv' => ['required', 'file', 'mimes:csv,txt'],
        ]);

        $path = $request->file('csv')->store('imports');

        ProcessProductImport::dispatch($path);

        return back()->with(
            'success',
            'Import sedang diproses di background.'
        );
    }

    public function importExcel(Request $request): RedirectResponse
    {
        $request->validate([
            'csv' => ['required', 'file', 'mimes:csv,txt'],
        ]);

        $import = new \App\Imports\ProductImport();

        Excel::import($import, $request->file('csv'));

        return back()->with(
            'success',
            'Import menggunakan Laravel Excel berhasil.'
        );
    }

    private function resolveDateRange(Request $request): array
    {
        $from = $request->date('from')?->startOfDay()
            ?? now()->startOfMonth();

        $to = $request->date('to')?->endOfDay()
            ?? now()->endOfDay();

        return [$from, $to];
    }

    private function resolveCategoryId(Request $request): ?int
    {
        $validated = $request->validate([
            'category_id' => [
                'nullable',
                'integer',
                'exists:categories,id',
            ],
        ]);

        if (empty($validated['category_id'])) {
            return null;
        }

        return (int) $validated['category_id'];
    }

    private function filteredQuery(
        $from,
        $to,
        ?int $categoryId = null
    ): Builder {
        return Transaction::with('user')
            ->whereBetween('created_at', [$from, $to])
            ->when($categoryId, function (Builder $query) use ($categoryId) {
                $query->whereHas('details.product', function (Builder $productQuery) use ($categoryId) {
                    $productQuery->where('category_id', $categoryId);
                });
            });
    }
}