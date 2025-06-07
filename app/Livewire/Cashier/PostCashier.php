<?php

namespace App\Livewire\Cashier;

use App\Models\Buyer;
use App\Models\Cashier;
use App\Models\Transaction;
use Livewire\Component;
use Illuminate\Support\Facades\DB;

class PostCashier extends Component
{
    // Basic properties
    public $activeCustomer = 0;
    public $customers = [];

    public $qty = 1;
    public $productName = '';
    public $costPrice = '';
    public $sellPrice = '';
    public $carType = '';
    public $carId = '';
    public $customerName = '';
    public $customerPhone = '';
    public $paymentMethod = 'cash';
    public $notes = '';
    public $transactionDate;
    public $items = [];
    public $totalItems = 0;
    public $totalCost = 0;
    public $totalSell = 0;
    public $profit = 0;


    protected $listeners = ['restoreCustomers'];

    public function mount()
    {

        $savedData = session('cashier_data');
        if ($savedData) {
            $this->customers = $savedData['customers'];
            $this->activeCustomer = $savedData['activeCustomer'];
        } else {
            $this->addNewCustomer();
        }
    }

    public function hydrate()
    {

        $this->dispatch('saveCustomers', [
            'customers' => $this->customers,
            'activeCustomer' => $this->activeCustomer
        ]);
    }

    public function dehydrate()
    {

        $this->dispatch('saveCustomers', [
            'customers' => $this->customers,
            'activeCustomer' => $this->activeCustomer
        ]);
    }

    public function updated()
    {

        $this->dispatch('customer-updated');
    }

    public function restoreCustomers($data)
    {
        if (!empty($data['customers'])) {
            $this->customers = $data['customers'];
            $this->activeCustomer = $data['activeCustomer'];
        } else {
            $this->addNewCustomer();
        }
    }

    public function addNewCustomer()
    {
        $newIndex = count($this->customers);
        $this->customers[] = [
            'id' => $newIndex,
            'carType' => '',
            'carId' => '',
            'customerName' => '',
            'customerPhone' => '',
            'paymentMethod' => 'cash',
            'transactionDate' => now()->format('Y-m-d\TH:i'),
            'notes' => '',
            'items' => [],
            'totalItems' => 0,
            'totalCost' => 0,
            'totalSell' => 0,
            'profit' => 0,
            'qty' => 1,
            'productName' => '',
            'costPrice' => '',
            'sellPrice' => '',
        ];

        $this->activeCustomer = $newIndex;
    }

    public function switchCustomer($index)
    {
        $this->activeCustomer = $index;
    }

    public function calculateRowTotal()
    {
        if (!isset($this->customers[$this->activeCustomer])) {
            return 0;
        }

        $customer = $this->customers[$this->activeCustomer];
        return (float) $customer['sellPrice'] * (int) $customer['qty'];
    }

    public function addItem()
    {
        $customer = &$this->customers[$this->activeCustomer];

        if (!$customer['productName'] || (int) $customer['qty'] <= 0) {
            return;
        }

        $customer['items'][] = [
            'id' => count($customer['items']) + 1,
            'qty' => (int) $customer['qty'],
            'name' => $customer['productName'],
            'costPrice' => (float) $customer['costPrice'],
            'sellPrice' => (float) $customer['sellPrice'],
            'total' => (float) $customer['sellPrice'] * (int) $customer['qty']
        ];

        $this->calculateTotals();
        $this->resetInputs();
    }

    public function removeItem($index)
    {
        if (isset($this->customers[$this->activeCustomer]['items'][$index])) {
            // Remove the item
            unset($this->customers[$this->activeCustomer]['items'][$index]);

            // Reindex the array
            $this->customers[$this->activeCustomer]['items'] = array_values($this->customers[$this->activeCustomer]['items']);

            // Recalculate totals
            $this->calculateTotals();
        }
    }

    private function calculateTotals()
    {
        $customer = &$this->customers[$this->activeCustomer];


        $customer['totalItems'] = collect($customer['items'])->sum('qty');

        $customer['totalCost'] = collect($customer['items'])->sum(function ($item) {
            return (float) $item['costPrice'] * (int) $item['qty'];
        });

        $customer['totalSell'] = collect($customer['items'])->sum(function ($item) {
            return (float) $item['sellPrice'] * (int) $item['qty'];
        });

        $customer['profit'] = $customer['totalSell'] - $customer['totalCost'];

        // Update component properties
        $this->totalItems = $customer['totalItems'];
        $this->totalCost = $customer['totalCost'];
        $this->totalSell = $customer['totalSell'];
        $this->profit = $customer['profit'];
    }

    private function resetInputs()
    {
        $customer = &$this->customers[$this->activeCustomer];
        $customer['qty'] = 1;
        $customer['productName'] = '';
        $customer['costPrice'] = '';
        $customer['sellPrice'] = '';
    }

    public function printNota()
    {
        $customer = $this->customers[$this->activeCustomer];

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
                    'product_id' => $item['id'], // Assuming you have product_id
                    'quantity' => $item['qty']
                ]);
            }

            DB::commit();

            // Dispatch print event
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

    public function render()
    {
        return view('livewire.cashier.post-cashier');
    }
}
