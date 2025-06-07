<?php

namespace App\Livewire\Cashier;

use App\Models\Buyer;
use App\Models\Cashier;
use App\Models\Product;
use App\Models\Transaction;
use Livewire\Component;
use Illuminate\Support\Facades\DB;

class PostCashier extends Component
{
    // Remove individual customer properties
    public $activeCustomer = 0;
    public $customers = [];

    // Keep only transaction-related properties
    public $paymentMethod = 'cash';
    public $notes = '';
    public $transactionDate;
    public $totalItems = 0;
    public $totalCost = 0;
    public $totalSell = 0;
    public $profit = 0;

    // Add temporary customer data property
    public $tempCustomerData = [
        'customerName' => '',
        'customerPhone' => '',
        'carType' => '',
        'carId' => ''
    ];

    public $products = [];
    public $search = '';


    protected $listeners = ['restoreCustomers'];

    public function mount()
    {
        // Get all products first
        $this->products = Product::select('id', 'title', 'modal', 'stock')
            ->get()
            ->toArray();

        // Set default transaction date
        $this->transactionDate = now()->format('Y-m-d\TH:i');

        // Initialize customers from session if exists
        $savedData = session('cashier_data');
        if ($savedData) {
            $this->customers = $savedData['customers'];
            $this->activeCustomer = $savedData['activeCustomer'];
            $this->loadCustomerData();
        } else {
            $this->addNewCustomer();
        }
    }

    public function hydrate()
    {
        // Ensure active customer data is loaded after component hydration
        if (isset($this->customers[$this->activeCustomer])) {
            $customer = $this->customers[$this->activeCustomer];
            $this->totalItems = $customer['totalItems'] ?? 0;
            $this->totalCost = $customer['totalCost'] ?? 0;
            $this->totalSell = $customer['totalSell'] ?? 0;
            $this->profit = $customer['profit'] ?? 0;
        }

        // Save to session after every update
        session([
            'cashier_data' => [
                'customers' => $this->customers,
                'activeCustomer' => $this->activeCustomer
            ]
        ]);
    }

    public function dehydrate()
    {
        // Save to session before component dehydrates
        session([
            'cashier_data' => [
                'customers' => $this->customers,
                'activeCustomer' => $this->activeCustomer
            ]
        ]);
    }

    public function updated($field)
    {
        // Handle customer data updates
        if (str_starts_with($field, 'customers.')) {
            $this->dispatch('customer-updated');
        }
    }

    public function restoreCustomers($data)
    {
        // Only restore if we don't have any customers
        if (empty($this->customers)) {
            $this->customers = $data['customers'];
            $this->activeCustomer = $data['activeCustomer'];

            // Restore active customer totals
            if (isset($this->customers[$this->activeCustomer])) {
                $customer = $this->customers[$this->activeCustomer];
                $this->totalItems = $customer['totalItems'];
                $this->totalCost = $customer['totalCost'];
                $this->totalSell = $customer['totalSell'];
                $this->profit = $customer['profit'];
            }
        }
    }

    public function addNewCustomer()
    {
        // Create new customer with empty data
        $this->customers[] = [
            'customerName' => '',
            'customerPhone' => '',
            'carType' => '',
            'carId' => '',
            'items' => [],
            'totalItems' => 0,
            'totalCost' => 0,
            'totalSell' => 0,
            'profit' => 0,
            'qty' => 1,
            'productId' => null, // Add product ID field
            'productName' => '',
            'costPrice' => '',
            'sellPrice' => ''
        ];

        // Switch to new customer
        $this->activeCustomer = count($this->customers) - 1;

        // Reset totals for new customer
        $this->totalItems = 0;
        $this->totalCost = 0;
        $this->totalSell = 0;
        $this->profit = 0;
    }

    public function switchCustomer($index)
    {
        // Save ALL current customer data including form inputs
        $this->customers[$this->activeCustomer] = [
            'customerName' => $this->customers[$this->activeCustomer]['customerName'] ?? '',
            'customerPhone' => $this->customers[$this->activeCustomer]['customerPhone'] ?? '',
            'carType' => $this->customers[$this->activeCustomer]['carType'] ?? '',
            'carId' => $this->customers[$this->activeCustomer]['carId'] ?? '',
            'items' => $this->customers[$this->activeCustomer]['items'] ?? [],
            'totalItems' => $this->totalItems,
            'totalCost' => $this->totalCost,
            'totalSell' => $this->totalSell,
            'profit' => $this->profit,
            'qty' => $this->customers[$this->activeCustomer]['qty'] ?? 1,
            'productName' => $this->customers[$this->activeCustomer]['productName'] ?? '',
            'costPrice' => $this->customers[$this->activeCustomer]['costPrice'] ?? '',
            'sellPrice' => $this->customers[$this->activeCustomer]['sellPrice'] ?? ''
        ];

        // Switch to new customer
        $this->activeCustomer = $index;

        // Load ALL new customer data
        if (isset($this->customers[$index])) {
            $customer = $this->customers[$index];
            $this->totalItems = $customer['totalItems'] ?? 0;
            $this->totalCost = $customer['totalCost'] ?? 0;
            $this->totalSell = $customer['totalSell'] ?? 0;
            $this->profit = $customer['profit'] ?? 0;
        }
        $this->loadCustomerData();
    }

    private function loadCustomerData()
    {
        $customer = $this->customers[$this->activeCustomer];
        $this->tempCustomerData = [
            'customerName' => $customer['customerName'] ?? '',
            'customerPhone' => $customer['customerPhone'] ?? '',
            'carType' => $customer['carType'] ?? '',
            'carId' => $customer['carId'] ?? ''
        ];
    }

    public function saveCustomerData()
    {
        $this->customers[$this->activeCustomer] = array_merge(
            $this->customers[$this->activeCustomer],
            [
                'customerName' => $this->tempCustomerData['customerName'],
                'customerPhone' => $this->tempCustomerData['customerPhone'],
                'carType' => $this->tempCustomerData['carType'],
                'carId' => $this->tempCustomerData['carId']
            ]
        );

        $this->dispatch('customer-saved');
    }

    public function printNota()
    {
        $customer = $this->customers[$this->activeCustomer];
        $customer['transactionDate'] = $this->transactionDate; // Add transaction date

        if (empty($customer['items'])) {
            return $this->addError('print', 'Belum ada item dalam nota!');
        }

        if (!$customer['customerName']) {
            return $this->addError('print', 'Nama pembeli harus diisi!');
        }

        try {
            DB::beginTransaction();

            // Create or update buyer
            $buyer = Buyer::create([
                'name' => $customer['customerName'],
                'phone_number' => $customer['customerPhone'],
                'car_number' => $customer['carId'],
                'car_type' => $customer['carType']
            ]);

            // Create cashier record
            $cashier = Cashier::create([
                'buyer_id' => $buyer->id,
                'total_buy' => $customer['totalSell'],
                'quantity' => $customer['totalItems'],
                'capital' => $customer['totalCost']
            ]);

            // Create transactions for each item
            foreach ($customer['items'] as $item) {
                Transaction::create([
                    'cashier_id' => $cashier->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['qty']
                ]);
            }

            DB::commit();

            // Dispatch print event with added transaction date
            $this->dispatch('print-receipt', content: view('livewire.cashier.print-nota', [
                'customer' => $customer,
                'cashier_id' => $cashier->id
            ])->render());

            // Clear current customer data after successful save
            $this->removeCustomer($this->activeCustomer);

        } catch (\Exception $e) {
            DB::rollBack();
            $this->addError('print', 'Gagal menyimpan data: ' . $e->getMessage());
        }
    }

    private function removeCustomer($index)
    {
        unset($this->customers[$index]);
        $this->customers = array_values($this->customers);

        if (empty($this->customers)) {
            $this->addNewCustomer();
        } else {
            $this->activeCustomer = 0;
        }
    }

    public function clearData()
    {
        session()->forget('cashier_data');
        $this->customers = [];
        $this->activeCustomer = 0;
        $this->addNewCustomer();
    }

    public function deleteCustomer($index)
    {
        // Don't delete if it's the only customer
        if (count($this->customers) <= 1) {
            return;
        }

        unset($this->customers[$index]);
        $this->customers = array_values($this->customers);

        // If we deleted the active customer, switch to the first one
        if ($index === $this->activeCustomer) {
            $this->activeCustomer = 0;
        }
        // If we deleted a customer before the active one, adjust the index
        elseif ($index < $this->activeCustomer) {
            $this->activeCustomer--;
        }
    }

    private function calculateRowTotal()
    {
        $customer = $this->customers[$this->activeCustomer];

        if (!isset($customer['qty']) || !isset($customer['sellPrice'])) {
            return 0;
        }

        $qty = (int) $customer['qty'];
        $sellPrice = (float) $customer['sellPrice'];

        return $qty * $sellPrice;
    }

    public function addItem()
    {
        $customer = &$this->customers[$this->activeCustomer];

        if (!isset($customer['productName']) || empty($customer['productName']) || !isset($customer['qty']) || $customer['qty'] <= 0) {
            return;
        }

        // Find product by name to get the real ID
        $product = Product::where('title', $customer['productName'])->first();
        if (!$product) {
            return;
        }

        $customer['items'][] = [
            'product_id' => $product->id, // Changed from 'id' to 'product_id'
            'id' => $product->id, // Keep this for backward compatibility
            'name' => $product['productName'],
            'qty' => (int) $customer['qty'],
            'costPrice' => (float) $customer['costPrice'],
            'sellPrice' => (float) $customer['sellPrice'],
            'total' => ((float) $customer['sellPrice'] * (int) $customer['qty'])
        ];

        // Update totals
        $this->totalItems += (int) $customer['qty'];
        $this->totalCost += ((float) $customer['costPrice'] * (int) $customer['qty']);
        $this->totalSell += ((float) $customer['sellPrice'] * (int) $customer['qty']);
        $this->profit = $this->totalSell - $this->totalCost;

        // Update customer totals
        $customer['totalItems'] = $this->totalItems;
        $customer['totalCost'] = $this->totalCost;
        $customer['totalSell'] = $this->totalSell;
        $customer['profit'] = $this->profit;

        // Reset input fields
        $customer['qty'] = 1;
        $customer['productName'] = '';
        $customer['costPrice'] = '';
        $customer['sellPrice'] = '';
    }

    public function selectProduct($id)
    {
        $product = Product::find($id);
        if ($product) {
            $customer = &$this->customers[$this->activeCustomer];
            $customer['productId'] = $product->id; // Store the product ID
            $customer['productName'] = $product->title;
            $customer['costPrice'] = $product->modal;
            // You can set default sell price based on modal if needed
            // $customer['sellPrice'] = $product->modal * 1.2; // 20% markup
        }
    }

    public function removeItem($index)
    {
        $customer = &$this->customers[$this->activeCustomer];
        $item = $customer['items'][$index];

        // Subtract from totals
        $this->totalItems -= $item['qty'];
        $this->totalCost -= ($item['costPrice'] * $item['qty']);
        $this->totalSell -= ($item['sellPrice'] * $item['qty']);
        $this->profit = $this->totalSell - $this->totalCost;

        // Update customer totals
        $customer['totalItems'] = $this->totalItems;
        $customer['totalCost'] = $this->totalCost;
        $customer['totalSell'] = $this->totalSell;
        $customer['profit'] = $this->profit;

        // Remove the item
        unset($customer['items'][$index]);
        $customer['items'] = array_values($customer['items']);
    }

    public function render()
    {
        return view('livewire.cashier.post-cashier');
    }
}
