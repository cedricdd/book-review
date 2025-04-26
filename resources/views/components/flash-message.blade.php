@props(['name'])

@php
    $classes = 'flex justify-between items-center p-4 m-2 text-sm border rounded-lg font-bold';

    $classes .=
        $name == 'failure' ? ' text-red-800 border-red-300 bg-red-50' : ' text-green-800 border-green-300 bg-green-50';
@endphp

@if (session($name))
    <div {{ $attributes->merge(['class' => $classes]) }} role="alert">
        <span>{{ session($name) }}</span>
        <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"
            id="Close-Circle--Streamline-Solar-Broken" height="24" width="24">
            <desc>Close Circle Streamline Icon: https://streamlinehq.com</desc>
            <path d="M14.5 9.49999 9.5 14.5m-0.00002 -5.00003L14.5 14.4999" stroke="#000000" stroke-linecap="round"
                stroke-width="1.5"></path>
            <path
                d="M7 3.33782C8.47087 2.48697 10.1786 2 12 2c5.5228 0 10 4.47715 10 10 0 5.5228 -4.4772 10 -10 10 -5.52285 0 -10 -4.4772 -10 -10 0 -1.8214 0.48697 -3.52913 1.33782 -5"
                stroke="#000000" stroke-linecap="round" stroke-width="1.5"></path>
        </svg>
    </div>
@endif

@push('footer')
    <script>
        function closeAlert() {
            const alertDiv = document.querySelector('[role="alert"]');
            if (alertDiv) {
                alertDiv.style.transition = 'opacity 0.5s';
                alertDiv.style.opacity = '0';
                setTimeout(() => alertDiv.remove(), 500);
            }
        }
        document.addEventListener('DOMContentLoaded', function() {
            const closeButtons = document.querySelectorAll('#Close-Circle--Streamline-Solar-Broken');
            closeButtons.forEach(button => {
                button.addEventListener('click', function() {
                    closeAlert();
                });
            });

            setTimeout(function() {
                closeAlert();
            }, 5000)
        });
    </script>
@endpush
