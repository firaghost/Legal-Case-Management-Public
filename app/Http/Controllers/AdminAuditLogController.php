<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AdminAuditLogController extends Controller
{
    public function index(Request $request)
    {
        $query = AuditLog::with('user:id,name,role')
            ->when($request->user, fn($q,$v)=>$q->whereHas('user', fn($qq)=>$qq->where('name','like',"%$v%")))
            ->when($request->action, fn($q,$v)=>$q->where('action', 'like', "%$v%"))
            ->when($request->role, fn($q,$v)=>$q->whereHas('user', fn($qq)=>$qq->where('role', $v)))
            ->when($request->from, fn($q,$v)=>$q->whereDate('created_at','>=',$v))
            ->when($request->to, fn($q,$v)=>$q->whereDate('created_at','<=',$v))
            ->latest();
        $perPage = $request->input('perPage', 50);
        return $query->paginate($perPage);
    }

    public function export(Request $request)
    {
        $format = $request->query('format', 'csv');
        $logs = $this->index($request)->getCollection();
        if($format==='pdf'){
            // simple pdf via dompdf
            $html = view('reports.audit_pdf', ['logs'=>$logs])->render();
            $pdf = \PDF::loadHTML($html);
            return $pdf->download('audit-logs.pdf');
        }
        // CSV stream
        $headers = [
            'Content-Type'=>'text/csv',
            'Content-Disposition'=>'attachment; filename="audit-logs.csv"',
        ];
        return new StreamedResponse(function()use($logs){
            $out=fopen('php://output','w');
            fputcsv($out,['User','Action','Resource','Timestamp','Role','IP']);
            foreach($logs as $l){
                fputcsv($out,[$l->user->name,$l->action,$l->resource,$l->created_at,$l->user->role,$l->ip_address]);
            }
            fclose($out);
        },200,$headers);
    }
}






