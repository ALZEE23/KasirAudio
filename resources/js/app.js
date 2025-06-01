import './bootstrap';
import Alpine from 'alpinejs';

if (!window.Alpine) {
    window.Alpine = Alpine;

    document.addEventListener('livewire:initialized', () => {
        Alpine.start();
    });
}

// Handle receipt printing
document.addEventListener('livewire:initialized', () => {
    Livewire.on('print-receipt', ({ content }) => {
        // Create new window
        const printWindow = window.open('', '_blank', 'width=800,height=600');

        // Write complete HTML document
        printWindow.document.write(`
            <!DOCTYPE html>
            <html>
                <head>
                    <meta charset="utf-8">
                    <title>Nota Pembayaran</title>
                    <style>
                        body {
                            margin: 0;
                            padding: 0;
                            font-family: monospace;
                        }
                        @media print {
                            @page {
                                margin: 0;
                                size: 80mm auto;
                            }
                        }
                    </style>
                </head>
                <body>
                    ${content}
                    <script>
                        // Auto print when loaded
                        window.onload = function() {
                            window.print();
                            // Close window after printing
                            window.onafterprint = function() {
                                window.close();
                            }
                        }
                    </script>
                </body>
            </html>
        `);

        // Close the document
        printWindow.document.close();
    });
});
