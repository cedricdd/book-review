@props(['term'])

<form action="#" accept-charset="UTF-8" method="GET" class="mb-4">
    <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2">
        <div class="flex-1">
            <input type="text" name="q" placeholder="Search for books..." class="border rounded px-4 py-2 w-full h-[40px]" value="{{ $term }}">
        </div>
        <div class="flex gap-x-2">
            <x-forms.button color='blue'>
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" id="Search--Streamline-Sharp-Material" height="24" width="24"><desc>Search Streamline Icon: https://streamlinehq.com</desc><path fill="#FFFFFF" d="m19.9 20.9751 -6.575 -6.575c-0.5 0.43335 -1.083 0.77085 -1.749 1.0125 -0.666 0.24165 -1.37465 0.3625 -2.126 0.3625 -1.80265 0 -3.32835 -0.625 -4.577 -1.875 -1.248665 -1.25 -1.873 -2.75835 -1.873 -4.525 0 -1.76665 0.625 -3.275 1.875 -4.525 1.25 -1.25 2.7625 -1.875 4.5375 -1.875 1.775 0 3.28335 0.625 4.525 1.875 1.24165 1.25 1.8625 2.7596 1.8625 4.52875 0 0.71415 -0.11665 1.4046 -0.35 2.07125 -0.23335 0.66665 -0.58335 1.29165 -1.05 1.875l6.6 6.55 -1.1 1.1Zm-10.475 -6.7c1.35415 0 2.50525 -0.47915 3.45325 -1.4375 0.94785 -0.95835 1.42175 -2.1125 1.42175 -3.4625s-0.4739 -2.50415 -1.42175 -3.4625c-0.948 -0.958335 -2.0991 -1.4375 -3.45325 -1.4375 -1.368 0 -2.53085 0.479165 -3.4885 1.4375C4.978835 6.87095 4.5 8.0251 4.5 9.3751s0.478835 2.50415 1.4365 3.4625c0.95765 0.95835 2.1205 1.4375 3.4885 1.4375Z" stroke-width="0.5"></path></svg>
                Search
            </x-forms.button>
            @if(!empty($term))
                <x-link-button href="{{ route('books.index') }}">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" id="Close--Streamline-Sharp-Material" height="24" width="24"><desc>Close Streamline Icon: https://streamlinehq.com</desc><path fill="#000000" d="m6.2248 18.825 -1.05 -1.05L10.9498 12l-5.775 -5.775 1.05 -1.05 5.775 5.775 5.775 -5.775 1.05 1.05L13.0498 12l5.775 5.775 -1.05 1.05 -5.775 -5.775 -5.775 5.775Z" stroke-width="0.5"></path></svg>
                    Clear
                </x-link-button>
            @endif
        </div>
    </div>
</form>