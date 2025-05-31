<?php

namespace App\Livewire\Cashier;

use Livewire\Component;

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

    // Add listener for browser events
    protected $listeners = ['restoreCustomers'];

    public function mount()
    {
        // Check session first
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
        // This runs after component hydration
        $this->dispatch('saveCustomers', [
            'customers' => $this->customers,
            'activeCustomer' => $this->activeCustomer
        ]);
    }

    public function dehydrate()
    {
        // This runs before component dehydration
        $this->dispatch('saveCustomers', [
            'customers' => $this->customers,
            'activeCustomer' => $this->activeCustomer
        ]);
    }

    public function updated()
    {
        // This will trigger Alpine's watcher
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
        // Add safety check
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

    private function calculateTotals()
    {
        $customer = &$this->customers[$this->activeCustomer];

        // Ensure values are numeric
        $customer['totalItems'] = collect($customer['items'])->sum('qty');

        $customer['totalCost'] = collect($customer['items'])->sum(function ($item) {
            return (float) $item['costPrice'] * (int) $item['qty'];
        });

        $customer['totalSell'] = collect($customer['items'])->sum(function ($item) {
            return (float) $item['sellPrice'] * (int) $item['qty'];
        });

        $customer['profit'] = $customer['totalSell'] - $customer['totalCost'];
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
        if (empty($this->items)) {
            return $this->addError('print', 'Belum ada item dalam nota!');
        }

        if (!$this->customerName) {
            return $this->addError('print', 'Nama pembeli harus diisi!');
        }

        // Add your print logic here
        $this->dispatch('print-nota');
    }

    // Optional: Clear data method
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
