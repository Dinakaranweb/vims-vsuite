<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Log;

use App\Models\Department;
use App\Models\Postal;
use App\Models\PostalForwarding;
use App\Models\ReplyPost;
use App\Models\User;

use Barryvdh\DomPDF\Facade\Pdf;
use Dompdf\Options;

use Carbon\Carbon;
use Mail;

use App\Http\Controllers\NotificationController;
use App\Jobs\SendTicketNotificationMail;

class PostalForwardingController extends Controller
{
    public function dispatchPostal(Request $request, $id)
    {
        //Log::info('Logged');
        $postal = PostalForwarding::findOrFail($id);
        $postal->status = 'Dispatched';
        $postal->dispatched_by = Auth::id();
        $postal->save();

        return response()->json([
            'success' => true,
            'status' => 'Dispatched',
            'dispatched_to' => $postal->forwarded_to,
            'dispatched_by' => Auth::user()->name // Assuming Auth::user()->name is available
        ]);
    }

    public function collectPostal(Request $request, $id)
    {
        //Log::info('Logged');
        $postal = PostalForwarding::findOrFail($id);
        $postal->status = 'Collected';
        $postal->collected_by = Auth::id();
        $postal->save();

        //$collected_by = User::find($postal->collected_by);

        return response()->json([
            'success' => true,
            'status' => 'Collected',
            'dispatched_to' => Auth::user()->name,
            'dispatched_by' => $postal->forwarded_by ? User::find($postal->forwarded_by)->name : 'N/A'
        ]);
    }

    public function undoDispatch(Request $request, $id)
    {
        $postal = PostalForwarding::findOrFail($id);
        $postal->status = 'Received'; // Revert to the original status
        $postal->dispatched_by = null;
        $postal->save();

        return response()->json([
            'success' => true,
            'status' => 'Received',
            'dispatched_to' => '',
            'dispatched_by' => ''
        ]);
    }

    public function undoCollect(Request $request, $id)
    {
        $postal = PostalForwarding::findOrFail($id);
        $postal->status = 'Dispatched'; // Revert to the original status
        $postal->collected_by = null;
        $postal->save();

        return response()->json([
            'success' => true,
            'status' => 'Dispatched',
            'dispatched_by' => User::FindorFail($postal->dispatched_by)->name,
            'dispatched_to' => ''
        ]);
    }
}
