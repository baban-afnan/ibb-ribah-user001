<?php

namespace App\Http\Controllers\Agency;

use App\Models\ServiceField;
use App\Models\AgentService;
use App\Models\Transaction;
use App\Models\Service;
use App\Models\Wallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Http\Controllers\Controller;

class FirstAccountController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();

        $service = Service::where('name', 'First Account')->first();
        $fields = $service ? $service->fields()->where('is_active', 1)->get() : collect();

        $query = AgentService::where('user_id', $user->id)
            ->where('service_type', 'first_account');

        if ($request->filled('search')) {
            $query->where('reference', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $submissions = $query->orderByDesc('submission_date')
            ->paginate(10)
            ->withQueryString();

        $wallet = Wallet::firstOrCreate(
            ['user_id' => $user->id],
            ['balance' => 0.00, 'status' => 'active']
        );

        return view('pages.first-account', compact(
            'submissions',
            'fields',
            'wallet',
            'service'
        ));
    }

    public function store(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'service_field'  => 'required|exists:service_fields,id',
            'bvn'            => 'required|string|size:11',
            'nin'            => 'required|string|size:11',
            'phone_number'   => 'required|string|max:15',
            'address'        => 'required|string|max:500',
            'lga'            => 'required|string|max:255',
            'state'          => 'required|string|max:255',
            'passport'       => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $wallet = Wallet::where('user_id', $user->id)->firstOrFail();

        if ($wallet->status !== 'active') {
            return back()->with([
                'status' => 'error',
                'message' => 'Your wallet is not active.',
            ])->withInput();
        }

        $serviceField = ServiceField::findOrFail($validated['service_field']);
        $role = $user->role ?? 'user';
        
        $price = $serviceField->prices()
            ->where('user_type', $role)
            ->value('price') ?? $serviceField->base_price;

        if ($wallet->balance < $price) {
            $msg = "Insufficient wallet balance. Required: NGN " . number_format($price, 2);
            return back()->withErrors(['wallet' => $msg])->withInput();
        }

        DB::beginTransaction();

        try {
            // Handle passport upload
            $passportName = 'passport_' . Str::slug($user->email) . '_' . time() . '.' . $request->file('passport')->getClientOriginalExtension();
            $path = $request->file('passport')->storeAs('uploads/passports', $passportName, 'public');
            $passportUrl = Storage::disk('public')->url($path);

            // Debit wallet
            $wallet->decrement('balance', $price);

            $transactionRef = 'FA' . date('ymdHis') . strtoupper(Str::random(4));
            $performedBy = trim("{$user->first_name} {$user->last_name}");

            $transaction = Transaction::create([
                'transaction_ref' => $transactionRef,
                'user_id' => $user->id,
                'amount' => $price,
                'description' => "First Account Opening - {$serviceField->field_name}",
                'type' => 'debit',
                'status' => 'completed',
                'performed_by' => $performedBy,
                'metadata' => [
                    'service' => 'First Account',
                    'service_field' => $serviceField->field_name,
                    'bvn' => $validated['bvn'],
                    'nin' => $validated['nin'],
                    'phone' => $validated['phone_number'],
                ],
            ]);

            AgentService::create([
                'reference' => $transactionRef,
                'user_id' => $user->id,
                'service_id' => $serviceField->service_id,
                'service_field_id' => $serviceField->id,
                'service_name' => 'First Account',
                'field_code' => $serviceField->field_code,
                'service_field_name' => $serviceField->field_name,
                'bvn' => $validated['bvn'],
                'nin' => $validated['nin'],
                'phone_number' => $validated['phone_number'],
                'address' => $validated['address'],
                'lga' => $validated['lga'],
                'state' => $validated['state'],
                'amount' => $price,
                'passport_url' => $passportUrl,
                'transaction_id' => $transaction->id,
                'submission_date' => now(),
                'status' => 'pending',
                'service_type' => 'first_account',
                'performed_by' => $performedBy,
            ]);

            DB::commit();

            return redirect()->route('first-account.index')->with([
                'status' => 'success',
                'message' => "Account opening request submitted successfully. Charged: NGN " . number_format($price, 2),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            if (isset($path)) {
                Storage::disk('public')->delete($path);
            }
            return back()->withErrors([
                'error' => 'Something went wrong: ' . $e->getMessage(),
            ])->withInput();
        }
    }
}
