<?php

namespace App\Livewire\Cashier;

use Livewire\Component;

class PostCashier extends Component
{
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

    public function mount()
    {
        $this->transactionDate = now()->format('Y-m-d\TH:i');
    }

    public function calculateRowTotal()
    {
        return (int) $this->qty * ((int) $this->sellPrice ?: 0);
    }

    public function addItem()
    {
        $this->validate([
            'productName' => 'required',
            'qty' => 'required|numeric|min:1',
            'costPrice' => 'required|numeric|min:0',
            'sellPrice' => 'required|numeric|min:0',
        ]);

        $this->items[] = [
            'id' => count($this->items) + 1,
            'qty' => $this->qty,
            'name' => $this->productName,
            'costPrice' => $this->costPrice,
            'sellPrice' => $this->sellPrice,
            'total' => $this->calculateRowTotal()
        ];

        $this->calculateTotals();
        $this->resetInputs();
    }

    public function removeItem($index)
    {
        unset($this->items[$index]);
        $this->items = array_values($this->items);
        $this->calculateTotals();
    }

    private function calculateTotals()
    {
        $this->totalItems = collect($this->items)->sum('qty');
        $this->totalCost = collect($this->items)->sum(fn($item) => $item['costPrice'] * $item['qty']);
        $this->totalSell = collect($this->items)->sum(fn($item) => $item['sellPrice'] * $item['qty']);
        $this->profit = $this->totalSell - $this->totalCost;
    }

    private function resetInputs()
    {
        $this->qty = 1;
        $this->productName = '';
        $this->costPrice = '';
        $this->sellPrice = '';
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

    public function render()
    {
        return view('livewire.cashier.post-cashier');
    }
}
