<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Role;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;

class UserController extends Controller
{
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->middleware('restrict.to.role:super_admin')->only(['index', 'create', 'store', 'edit', 'update', 'destroy']);
        $this->middleware('auth')->only(['profile', 'updateProfile']);
        $this->userService = $userService;
    }

    public function index(Request $request)
    {
        $this->authorize('viewAny', \App\Models\User::class);
        $perPage = $request->input('per_page', 10);
        $search = $request->input('search');

        $users = $this->userService->getPaginatedNonClientUsers($search, $perPage);

        if ($request->ajax()) {
            $table = View::make('users.partials.table', compact('users'))->render();
            $pagination = $users->appends(['per_page' => $perPage, 'search' => $search])->links('pagination::bootstrap-5')->render();
            return response()->json([
                'table' => $table,
                'pagination' => $pagination,
                'first_item' => $users->firstItem() ?? 0,
                'last_item' => $users->lastItem() ?? 0,
                'total' => $users->total()
            ]);
        }

        return view('users.index', compact('users'));
    }

    public function create()
    {
        $this->authorize('create', \App\Models\User::class);
        $roles = Role::whereIn('name', ['super_admin', 'ceo'])->get();
        return view('users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $this->authorize('create', \App\Models\User::class);
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255',
            'password' => 'required|string|min:8|confirmed',
            'role_id' => 'required|exists:roles,id|in:1,2',
            'status' => 'required|in:active,inactive',
            'position' => 'nullable|in:Engineer,Technician,CEO,Other,None',
        ], [
            'first_name.required' => 'The first name is required.',
            'first_name.string' => 'The first name must be a string.',
            'first_name.max' => 'The first name may not be greater than 255 characters.',
            'last_name.required' => 'The last name is required.',
            'last_name.string' => 'The last name must be a string.',
            'last_name.max' => 'The last name may not be greater than 255 characters.',
            'email.required' => 'The email is required.',
            'email.email' => 'The email must be a valid email address.',
            'email.max' => 'The email may not be greater than 255 characters.',
            'password.required' => 'The password is required.',
            'password.min' => 'The password must be at least 8 characters.',
            'password.confirmed' => 'The password confirmation does not match.',
            'role_id.required' => 'The role is required.',
            'role_id.exists' => 'The selected role is invalid.',
            'role_id.in' => 'The role must be either super_admin or ceo.',
            'status.required' => 'The status is required.',
            'status.in' => 'The status must be active or inactive.',
            'status.in' => 'The position must be Engineer or Technician, CEO, Other or None!.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors())->withInput();
        }

        try {
            $this->userService->createNonClientUser($validator->validated(), $request->user());
            return redirect()->route('users.index')->with('success', 'User created or reactivated successfully');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }

    public function edit($id)
    {
        $user = $this->userService->getUser($id);
        $this->authorize('update', $user);
        if ($user->isClient()) {
            return redirect()->route('users.index')->with('error', 'Cannot edit client users here');
        }
        $roles = Role::whereIn('name', ['super_admin', 'ceo'])->get();
        return view('users.edit', compact('user', 'roles'));
    }

    public function update(Request $request, $id)
    {
        $user = $this->userService->getUser($id);
        $this->authorize('update', $user);
        if ($user->isClient()) {
            return redirect()->route('users.index')->with('error', 'Cannot update client users here');
        }
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'password' => 'nullable|string|min:8|confirmed',
            'role_id' => 'required|exists:roles,id|in:1,2',
            'status' => 'required|in:active,inactive',
            'position' => 'nullable|in:Engineer,Technician,CEO,Other,None',
        ], [
            'first_name.required' => 'The first name is required.',
            'first_name.string' => 'The first name must be a string.',
            'first_name.max' => 'The first name may not be greater than 255 characters.',
            'last_name.required' => 'The last name is required.',
            'last_name.string' => 'The last name must be a string.',
            'last_name.max' => 'The last name may not be greater than 255 characters.',
            'password.min' => 'The password must be at least 8 characters.',
            'password.confirmed' => 'The password confirmation does not match.',
            'role_id.required' => 'The role is required.',
            'role_id.exists' => 'The selected role is invalid.',
            'role_id.in' => 'The role must be either super_admin or ceo.',
            'status.required' => 'The status is required.',
            'status.in' => 'The status must be active or inactive.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors())->withInput();
        }

        try {
            $this->userService->updateNonClientUser($id, $validator->validated(), $request->user());
            return redirect()->route('users.index')->with('success', 'User updated successfully');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }

    public function destroy(Request $request, $id)
    {
        $user = $this->userService->getUser($id);
        $this->authorize('delete', $user);
        try {
            $this->userService->deleteUser($id, $request->user());
            return redirect()->route('users.index')->with('success', 'User deleted successfully');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function clientIndex(Request $request)
    {
        $this->authorize('viewAny', \App\Models\User::class);
        $perPage = $request->input('per_page', 10);
        $search = $request->input('search');

        $query = User::with(['role', 'company'])->where('role_id', 3)->whereNull('deleted_at');
        if ($request->user()->hasRole('client')) {
            $query->where('company_id', $request->user()->company_id);
        }
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->whereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%$search%"])
                    ->orWhere('email', 'like', "%$search%")
                    ->orWhereHas('company', function ($q) use ($search) {
                        $q->where('name', 'like', "%$search%");
                    });
            });
        }

        $clients = $query->orderBy('id', 'asc')->paginate($perPage);

        if ($request->ajax()) {
            $table = View::make('clients.partials.table', compact('clients'))->render();
            $pagination = $clients->appends(['per_page' => $perPage, 'search' => $search])->links('pagination::bootstrap-5')->render();
            return response()->json([
                'table' => $table,
                'pagination' => $pagination,
                'first_item' => $clients->firstItem() ?? 0,
                'last_item' => $clients->lastItem() ?? 0,
                'total' => $clients->total()
            ]);
        }

        return view('clients.index', compact('clients'));
    }

    public function clientShow($id)
    {
        $this->authorize('view', \App\Models\User::class);
        $client = $this->userService->getUser($id);
        if (!$client->isClient()) {
            return redirect()->route('clients.index')->with('error', 'User is not a client');
        }
        return view('clients.show', compact('client'));
    }

    public function clientCreate()
    {
        $this->authorize('create', \App\Models\User::class);
        $companies = Company::all();
        return view('clients.create', compact('companies'));
    }

    public function clientStore(Request $request)
    {
        $this->authorize('create', \App\Models\User::class);
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'company_name' => 'required|string|max:255|unique:companies,name',
            'status' => 'required|in:active,inactive',
            'phone' => 'nullable|string|max:20',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ], [
            'first_name.required' => 'The first name is required.',
            'first_name.string' => 'The first name must be a string.',
            'first_name.max' => 'The first name may not be greater than 255 characters.',
            'last_name.required' => 'The last name is required.',
            'last_name.string' => 'The last name must be a string.',
            'last_name.max' => 'The last name may not be greater than 255 characters.',
            'email.required' => 'The email is required.',
            'email.email' => 'The email must be a valid email address.',
            'email.unique' => 'This email is already registered.',
            'email.max' => 'The email may not be greater than 255 characters.',
            'password.required' => 'The password is required.',
            'password.min' => 'The password must be at least 8 characters.',
            'password.confirmed' => 'The password confirmation does not match.',
            'company_name.required' => 'The company name is required.',
            'company_name.string' => 'The company name must be a string.',
            'company_name.max' => 'The company name may not be greater than 255 characters.',
            'company_name.unique' => 'This company name is already taken.',
            'status.required' => 'The status is required.',
            'status.in' => 'The status must be active or inactive.',
            'phone.string' => 'The phone number must be a string.',
            'phone.max' => 'The phone number may not be greater than 20 characters.',
            'image.image' => 'The logo must be an image.',
            'image.mimes' => 'The logo must be a file of type: jpeg, png, jpg.',
            'image.max' => 'The logo may not be greater than 2MB.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors())->withInput();
        }

        try {
            $validated = $validator->validated();
            $company = Company::create([
                'name' => $validated['company_name'],
                'contact_info' => [
                    'email' => $validated['email'],
                    'phone' => $validated['phone'] ?? null,
                ],
            ]);
            $validated['company_id'] = $company->id;
            unset($validated['company_name']);
            $this->userService->createClientUser($validated, $request->user());
            return redirect()->route('clients.index')->with('success', 'Client created successfully');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }

    public function clientEdit(Request $request, $id)
    {
        $this->authorize('update', \App\Models\User::class);
        $client = $this->userService->getUser($id);
        if (!$client->isClient()) {
            return redirect()->route('clients.index')->with('error', 'Cannot edit non-client users here');
        }
        if ($request->user()->hasRole('client') && $client->company_id !== $request->user()->company_id) {
            return redirect()->route('clients.index')->with('error', 'You can only edit clients from your company');
        }
        $companies = Company::all();
        return view('clients.edit', compact('client', 'companies'));
    }

    public function clientUpdate(Request $request, $id)
    {
        $this->authorize('update', \App\Models\User::class);
        $client = $this->userService->getUser($id);
        if (!$client->isClient()) {
            return redirect()->route('clients.index')->with('error', 'Cannot update non-client users here');
        }
        if ($request->user()->hasRole('client') && $client->company_id !== $request->user()->company_id) {
            return redirect()->route('clients.index')->with('error', 'You can only update clients from your company');
        }
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $id,
            'password' => 'nullable|string|min:8|confirmed',
            'company_name' => 'required|string|max:255|unique:companies,name,' . $client->company_id,
            'status' => 'required|in:active,inactive',
            'phone' => 'nullable|string|max:20',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'remove_image' => 'nullable|boolean',
        ], [
            'first_name.required' => 'The first name is required.',
            'first_name.string' => 'The first name must be a string.',
            'first_name.max' => 'The first name may not be greater than 255 characters.',
            'last_name.required' => 'The last name is required.',
            'last_name.string' => 'The last name must be a string.',
            'last_name.max' => 'The last name may not be greater than 255 characters.',
            'email.required' => 'The email is required.',
            'email.email' => 'The email must be a valid email address.',
            'email.unique' => 'This email is already registered.',
            'email.max' => 'The email may not be greater than 255 characters.',
            'password.min' => 'The password must be at least 8 characters.',
            'password.confirmed' => 'The password confirmation does not match.',
            'company_name.required' => 'The company name is required.',
            'company_name.string' => 'The company name must be a string.',
            'company_name.max' => 'The company name may not be greater than 255 characters.',
            'status.required' => 'The status is required.',
            'status.in' => 'The status must be active or inactive.',
            'phone.string' => 'The phone number must be a string.',
            'phone.max' => 'The phone number may not be greater than 20 characters.',
            'image.image' => 'The logo must be an image.',
            'image.mimes' => 'The logo must be a file of type: jpeg, png, jpg.',
            'image.max' => 'The logo may not be greater than 2MB.',
            'remove_image.boolean' => 'The remove image option must be a boolean value.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors())->withInput();
        }

        try {
            $this->userService->updateClientUser($id, $validator->validated(), $request->user());
            return redirect()->route('clients.index')->with('success', 'Client updated successfully');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }

    public function clientDestroy(Request $request, $id)
    {
        $this->authorize('delete', \App\Models\User::class);
        try {
            $this->userService->deleteUser($id, $request->user());
            return redirect()->route('clients.index')->with('success', 'Client deleted successfully');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function profile()
    {
        $user = auth()->user();
        return view('users.profile', compact('user'));
    }

    public function updateProfile(Request $request)
    {
        $user = auth()->user();
        $rules = [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'password' => 'nullable|string|min:8|confirmed',
        ];

        if ($user->isClient()) {
            $rules['image'] = 'nullable|image|mimes:jpeg,png,jpg|max:2048';
            $rules['remove_image'] = 'nullable|boolean';
        }

        $messages = [
            'first_name.required' => 'The first name is required.',
            'first_name.string' => 'The first name must be a string.',
            'first_name.max' => 'The first name may not be greater than 255 characters.',
            'last_name.required' => 'The last name is required.',
            'last_name.string' => 'The last name must be a string.',
            'last_name.max' => 'The last name may not be greater than 255 characters.',
            'password.min' => 'The password must be at least 8 characters.',
            'password.confirmed' => 'The password confirmation does not match.',
            'image.image' => 'The company logo must be an image.',
            'image.mimes' => 'The company logo must be a file of type: jpeg, png, jpg.',
            'image.max' => 'The company logo may not be greater than 2MB.',
            'remove_image.boolean' => 'The remove image option must be a boolean value.',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors())->withInput();
        }

        try {
            $this->userService->updateProfile($validator->validated(), $user);
            return redirect()->route('profile')->with('success', 'Profile updated successfully');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }
}
