<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

use App\Models\Department;
use App\Models\Ticket;
use App\Models\Ping;
use App\Models\Notification;
use App\Models\User;

class DepartmentController extends Controller
{
    public function addDepts(){

        $activeMenu = "depts";
        $activeDropdown = "add_depts";

        return view('frontend.admin.depts.add-depts', compact('activeMenu', 'activeDropdown'));
    }

    public function storeDepts(Request $request){
        
        $messages = [
            'dept_label.unique' => 'The label has already been taken.',
            'dept_name.unique' => 'The department name has already been taken.',
        ];

        $request->validate([
            'dept_label' => 'required|unique:departments,dept_label',
            'dept_name' => 'required|unique:departments,dept_name',
        ], $messages);

        $randomNumber = mt_rand(1000, 9999);
        $dept_id = 'DPT-' . str_pad($randomNumber, 4, '0', STR_PAD_LEFT);

        Department::create([
            'dept_id' => $dept_id,
            'dept_name' => $request->dept_name,
            'dept_label' => $request->dept_label,
            'created_at' => now()
        ]);

        $notification = array(
            'message' => 'Department added',
            'alert-type' => 'success'
        );
 
        return redirect()->route('view-depts')->with($notification);
 
    }

    public function viewDepts(){

        $activeMenu = "depts";
        $activeDropdown = "view_depts";

        $depts = Department::all();

        return view('frontend.admin.depts.view-depts', compact('activeMenu', 'activeDropdown', 'depts'));
    }

    public function editDept($id){

        $activeMenu = "depts";
        $activeDropdown = "view_depts";

        $emp = Department::findOrFail($id);

        return view('frontend.admin.depts.edit-dept', compact('activeMenu', 'activeDropdown', 'emp'));

    }

    public function updateDept(Request $request){

        $messages = [
            'dept_label.unique' => 'The label has already been taken.',
            'dept_name.unique' => 'The department name has already been taken.',
            'dept_id.unique' => 'The department id has already been taken.',
        ];

        $user = Department::findOrFail($request->id);

        $validatedData = $request->validate([
            'dept_name' => [
                'required',
                Rule::unique('departments', 'dept_name')->ignore($user->id),
            ],
            'dept_label' => [
                'required',
                Rule::unique('departments', 'dept_label')->ignore($user->id),
            ],
            'dept_id' => [
                'required',
                Rule::unique('departments', 'dept_id')->ignore($user->id),
            ],
            
        ], $messages);

        Ticket::where('ticket_to', $user->dept_label)->update(['ticket_to' => $request->dept_label]);
        Ticket::where('ticket_from', $user->dept_label)->update(['ticket_from' => $request->dept_label]);
        
        User::where('department', $user->dept_label)->update(['department' => $request->dept_label]);
        
        Notification::where('to', $user->dept_label)->update(['to' => $request->dept_label]);
        
        Ping::where('ping_to', $user->dept_label)->update(['ping_to' => $request->dept_label]);

        $user->update([
            'dept_name' => $request->dept_name,
            'dept_label' => $request->dept_label,
            'dept_id' => $request->dept_id,
            'updated_at' => now()
        ]);

        //dd($user->dept_label);

        $notification = array(
            'message' => 'Department Updated!',
            'alert-type' => 'success'
        );
 
        return redirect()->route('view-depts')->with($notification);

    }

    public function deleteDept($id){

        $user = Department::findOrFail($id);

        if ($user) {
                        
            Department::findOrFail($id)->update(['is_active' => false]);

            $user->delete();

            $alert = array(
                'message' => 'Dept Deleted',
                'alert-type' => 'success'
            );

            return redirect()->back()->with($alert);
            
        } else {
            
            $alert = array(
                'message' => 'Error! Try Later',
                'alert-type' => 'error'
            );

            return redirect()->back()->with($alert);

        }

    }

    public function exDepts(){

        $activeMenu = "depts";
        $activeDropdown = "ex_depts";

        $depts = Department::onlyTrashed()->get();

        return view('frontend.admin.depts.ex-depts', compact('activeMenu', 'activeDropdown', 'depts'));

    }

    public function recoverDept($id){

        $user = Department::withTrashed()->find($id);
        $user->restore();

        Department::findOrFail($id)->update(['is_active' => true]);

        $alert = array(
            'message' => 'Department Recovered!',
            'alert-type' => 'success'
        );

        return redirect()->route('view-depts')->with($alert);
    }

    public function changeDeptStatus($id){

        $dept = Department::findOrFail($id);

        //dd($user);

        if($dept->is_active){

            $status = Department::findOrFail($id)->update(['is_active' => false]);

            //dd(Department::findOrFail($id));

            $alert = array(
                'message' => 'Department Deactivated!',
                'alert-type' => 'success'
            );

            return redirect()->back()->with($alert);

        }else{
            
            Department::findOrFail($id)->update(['is_active' => true]);

            $alert = array(
                'message' => 'Department Activated!',
                'alert-type' => 'success'
            );

            return redirect()->back()->with($alert);
        
        }

    }

    public function testSearchDepartments(Request $request)
    {
        $query = $request->input('query');
        
        // Search departments based on the input query
        $departments = Department::where('dept_label', 'LIKE', "%{$query}%")->pluck('dept_label');
        
        return response()->json($departments);
    }
    
    public function TestSearch2Departments(Request $request)
    {
        $query = $request->input('query');
        
        // Search departments with their heads
        $departments = Department::where('dept_label', 'LIKE', "%{$query}%")
            ->with(['head' => function($query) {
                $query->select('id', 'name', 'department');
            }])
            ->get()
            ->map(function($dept) {
                return [
                    'dept_label' => $dept->dept_label,
                    'head_name' => $dept->head ? $dept->head->name : 'No head assigned'
                ];
            });
        
        return response()->json($departments);
    }
    
    public function searchDepartments(Request $request)
    {
        $query = $request->input('query');
        $user = Auth::user();
        
        // Search departments with their heads
        if(($user->role != 'SuperAdmin' && $user->department != 'Purchase') && ($user->department != 'Medical Director' || $user->department != 'General Manager')) {
            // For non-SuperAdmin: exclude Chancellor from both department name and head name searches
            $departments = Department::where(function($q) use ($query) {
                    $q->where('dept_label', 'LIKE', "%{$query}%")
                    ->where('dept_label', '!=', 'Chairman')
                    ->where('dept_label', '!=', 'Chairman Office')
                    ->where('dept_label', '!=', 'STB Office')
                    ->where('dept_label', '!=', 'General Manager - Admin');
                })
                ->orWhere(function($q) use ($query) {
                    $q->whereHas('head', function($q) use ($query) {
                        $q->where('name', 'LIKE', "%{$query}%");
                    })
                    ->where('dept_label', '!=', 'Chairman')
                    ->where('dept_label', '!=', 'Chairman Office')
                    ->where('dept_label', '!=', 'STB Office')
                    ->where('dept_label', '!=', 'General Manager - Admin');
                })
                ->with(['head' => function($query) {
                    $query->select('id', 'name', 'department');
                }])
                ->get()
                ->map(function($dept) {
                    return [
                        'dept_label' => $dept->dept_label,
                        'head_name' => $dept->head ? $dept->head->name : 'No head assigned',
                        'type' => 'department'
                    ];
                });

        } elseif ($user->role == 'HOD' && $user->department == 'Purchase') {
            // For SuperAdmin: show all including Chancellor
            $departments = Department::where(function($q) use ($query) {
                    $q->where('dept_label', 'LIKE', "%{$query}%")
                    ->where('dept_label', '!=', 'Chairman')
                    ->where('dept_label', '!=', 'STB Office');
                })
                ->orWhere(function($q) use ($query) {
                    $q->whereHas('head', function($q) use ($query) {
                        $q->where('name', 'LIKE', "%{$query}%");
                    })
                    ->where('dept_label', '!=', 'Chairman')
                    ->where('dept_label', '!=', 'STB Office');
                })
                ->with(['head' => function($query) {
                    $query->select('id', 'name', 'department');
                }])
                ->get()
                ->map(function($dept) {
                    return [
                        'dept_label' => $dept->dept_label,
                        'head_name' => $dept->head ? $dept->head->name : 'No head assigned',
                        'type' => 'department'
                    ];
                });
        } else {
            // For SuperAdmin: show all including Chancellor
            $departments = Department::where('dept_label', 'LIKE', "%{$query}%")
                ->orWhereHas('head', function($q) use ($query) {
                    $q->where('name', 'LIKE', "%{$query}%");
                })
                ->with(['head' => function($query) {
                    $query->select('id', 'name', 'department');
                }])
                ->get()
                ->map(function($dept) {
                    return [
                        'dept_label' => $dept->dept_label,
                        'head_name' => $dept->head ? $dept->head->name : 'No head assigned',
                        'type' => 'department'
                    ];
                });
        }
        
        // Also search users who might be department heads but not linked yet
        $usersQuery = User::where('name', 'LIKE', "%{$query}%")
            ->where(function($q) {
                $q->where('designation', 'like', '%head%')
                ->orWhere('designation', 'like', '%manager%')
                ->orWhere('designation', 'like', '%director%')
                ->orWhere('role', 'admin');
            });

        // Also exclude users from Chancellor department for non-SuperAdmin
        if($user->role != 'SuperAdmin') {
            $usersQuery->where('department', '!=', 'Chairman')->where('department', '!=', 'Chairman Office')->where('department', '!=', 'General Manager - Admin')->where('department', '!=', 'STB Office');
        }

        $users = $usersQuery->select('name', 'department')
            ->get()
            ->map(function($user) {
                return [
                    'dept_label' => $user->department,
                    'head_name' => $user->name,
                    'type' => 'user'
                ];
            });
        
        // Combine and remove duplicates
        $results = $departments->concat($users)->unique(function ($item) {
            return $item['dept_label'].$item['head_name'];
        });
        
        return response()->json($results);
    }
}
