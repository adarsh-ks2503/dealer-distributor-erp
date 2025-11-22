<?php

namespace App\Http\Controllers;

use App\Models\NotificationModel;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use Spatie\Permission\Models\Role;
use DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Arr;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{

    function __construct()
    {
        $this->middleware('permission:UserManagement-Index', ['only' => ['index']]);
        $this->middleware('permission:UserManagement-Create', ['only' => ['create', 'store']]);
        $this->middleware('permission:UserManagement-Edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:UserManagement-InActive', ['only' => ['destroy']]);
        $this->middleware('permission:UserManagement-View', ['only' => ['show']]);
    }

    public function index(Request $request): View
    {
        // $data = User::where('user_type', 'web')->latest('updated_at')->withTrashed()->get();
        $data = User::where('user_type', 'web')
            ->where('id', '!=', Auth::id())
            ->latest('updated_at')
            ->withTrashed()
            ->get();
        return view('users.index', compact('data'));
    }



    public function create(): View
    {
        $roles = Role::pluck('name', 'name')->all();

        return view('users.create', compact('roles'));
    }


    public function store(Request $request): RedirectResponse
    {

        Log::info('User creation request received.', $request->all());

        $this->validate($request, [
            'name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'mobile' => 'required|digits:10|unique:users,mobile',
            'email' => 'required|email|unique:users,email',
            'roles' => 'required|array',
            'password' => 'required|string|min:8|same:confirm-password',
            'confirm-password' => 'required|string|min:8'
        ]);

        $input = $request->all();


        $input['password'] = Hash::make($input['password']);
        $lastEmployee = User::whereNotNull('employee_id')
            ->orderByDesc('employee_id')
            ->withTrashed()
            ->first();

        if ($lastEmployee && preg_match('/EMP(\d+)/', $lastEmployee->employee_id, $matches)) {
            $lastNumber = (int) $matches[1];
            $nextNumber = $lastNumber + 1;

            // Use same padding length as last number's digit count
            $padLength = strlen($matches[1]);

            $newEmployeeId = 'EMP' . str_pad($nextNumber, $padLength, '0', STR_PAD_LEFT);
        } else {
            $newEmployeeId = 'EMP0001';
        }

        $input['employee_id'] = $newEmployeeId;
        $input['user_type'] = 'web';


        $user = User::create($input);

        $user->assignRole($request->input('roles'));

        return redirect()->route('users.index')
            ->with('success', 'User created successfully');
    }



    public function show($id): View
    {
        $user = User::withTrashed()->find($id);

        return view('users.show', compact('user'));
    }


    public function edit($id): View
    {
        $user = User::find($id);
        $roles = Role::all();
        $userRoles = $user->roles->pluck('id')->all();

        // dd($roles);

        return view('users.edit', compact('user', 'roles', 'userRoles'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id): RedirectResponse
    {
        $validate = $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email,' . $id,
            'mobile' => 'required|digits:10|unique:users,mobile,' . $id,
            'password' => 'nullable|min:8|same:confirm-password',
            'roles' => 'required|array', // Ensure roles is an array
        ]);

        $input = $request->except(['_token', '_method']); // Exclude unnecessary fields

        if (!empty($input['password'])) {
            $input['password'] = Hash::make($input['password']);
        } else {
            unset($input['password']);
        }

        $user = User::findOrFail($id);
        $user->update($input);


        // Ensure proper role IDs are passed
        $roleIds = array_map('intval', $validate['roles']);

        // Sync roles
        $user->roles()->sync($roleIds);

        return redirect()->route('users.index')
            ->with('update', 'User Updated Successfully');
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id): RedirectResponse
    {
        $user = User::findOrFail($id);
        $user->delete(); // ✅ This performs a soft delete if SoftDeletes is used

        return redirect()->route('users.index')
            ->with('success', 'User deactivate successfully');
    }


    public function resoter($id): RedirectResponse
    {
        $user = User::onlyTrashed()->findOrFail($id);
        $user->restore();

        return redirect()->route('users.index')
            ->with('success', 'User restored successfully');
    }
}
