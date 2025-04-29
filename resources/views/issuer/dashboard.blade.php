<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Issuer Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="flex items-center mb-6">
                        <div>
                            <h3 class="text-lg font-medium text-gray-900">Bienvenue, {{ $user->name }}</h3>
                            <p class="mt-1 text-sm text-gray-500">
                                {{ $user->position }}
                                {{ $organization ? '- ' . $organization->name : '' }}
                            </p>
                        </div>
                    </div>

                    <h4 class="text-md font-medium text-gray-700 mt-6">Vos créneaux planifiés</h4>

                    @if ($timeSlotsByDate->isEmpty())
                        <p class="text-sm text-gray-500 mt-2">Vous n'avez aucun créneau planifié.</p>
                    @else
                        @foreach ($timeSlotsByDate as $date => $timeSlots)
                            <div class="mt-4">
                                <h5 class="text-sm font-medium text-gray-700">
                                    {{ \Carbon\Carbon::parse($date)->translatedFormat('l d F Y') }}</h5>

                                <div class="mt-2 overflow-hidden shadow ring-1 ring-black ring-opacity-5 md:rounded-lg">
                                    <table class="min-w-full divide-y divide-gray-300">
                                        <thead class="bg-gray-50">
                                            <tr>
                                                <th scope="col"
                                                    class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900">
                                                    Heure</th>
                                                <th scope="col"
                                                    class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">
                                                    Disponibilité</th>
                                                <th scope="col"
                                                    class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">
                                                    Investisseurs</th>
                                                <th scope="col"
                                                    class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">
                                                    Salle</th>
                                                <th scope="col"
                                                    class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">
                                                    Questions</th>
                                                <th scope="col" class="relative py-3.5 pl-3 pr-4 text-right">Actions
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-200 bg-white">
                                            @foreach ($timeSlots as $timeSlot)
                                                @php
                                                    $investors = $timeSlot->investors();
                                                @endphp
                                                <tr>
                                                    <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm text-gray-900">
                                                        {{ $timeSlot->start_time->format('H:i') }}
                                                    </td>
                                                    <td
                                                        class="whitespace-nowrap px-3 py-4 text-sm font-medium text-gray-900">
                                                        @if ($timeSlot->availability)
                                                            <span
                                                                class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                                Disponible
                                                            </span>
                                                        @else
                                                            <span
                                                                class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                                                Non disponible
                                                            </span>
                                                        @endif
                                                    </td>
                                                    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                                        {{ $investors->count() }} investisseur(s)
                                                    </td>
                                                    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                                        {{ $timeSlot->room->name }}
                                                    </td>
                                                    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                                        {{ $timeSlot->questions->count() }}
                                                    </td>
                                                    <td
                                                        class="relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium">
                                                        <a href="#" class="text-indigo-600 hover:text-indigo-900">
                                                            Voir
                                                        </a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @endforeach
                    @endif

                    <h4 class="text-md font-medium text-gray-700 mt-8">Questions récentes</h4>

                    @php
                        // Récupérer toutes les questions pour les créneaux de cet émetteur
                        $recentQuestions = collect();
                        foreach ($timeSlots as $timeSlot) {
                            $recentQuestions = $recentQuestions->merge($timeSlot->questions);
                        }
                        $recentQuestions = $recentQuestions->sortByDesc('created_at')->take(5);
                    @endphp

                    @if ($recentQuestions->isEmpty())
                        <p class="text-sm text-gray-500 mt-2">Aucune question reçue.</p>
                    @else
                        <div class="mt-2 overflow-hidden shadow ring-1 ring-black ring-opacity-5 md:rounded-lg">
                            <table class="min-w-full divide-y divide-gray-300">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col"
                                            class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900">Date
                                        </th>
                                        <th scope="col"
                                            class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Créneau
                                        </th>
                                        <th scope="col"
                                            class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">
                                            Investisseur</th>
                                        <th scope="col"
                                            class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Question
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 bg-white">
                                    @foreach ($recentQuestions as $question)
                                        <tr>
                                            <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm text-gray-900">
                                                {{ $question->created_at->format('d/m/Y H:i') }}
                                            </td>
                                            <td class="whitespace-nowrap px-3 py-4 text-sm font-medium text-gray-900">
                                                <a href="#" class="text-indigo-600 hover:text-indigo-900">
                                                    {{ $question->timeSlot->start_time->format('d/m/Y H:i') }}
                                                </a>
                                            </td>
                                            <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                                {{ $question->user->name }}
                                                ({{ $question->user->organization->name }})
                                            </td>
                                            <td class="px-3 py-4 text-sm text-gray-500">
                                                {{ $question->question }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif

                    @if ($timeSlots->isNotEmpty())
                        <div class="mt-6">
                            <h4 class="text-md font-medium text-gray-700">Répartition des questions par créneau</h4>
                            <div class="mt-4 grid gap-4 grid-cols-1 md:grid-cols-2">
                                @foreach ($timeSlots->take(4) as $timeSlot)
                                    <div class="bg-white p-4 rounded-lg shadow">
                                        <h5 class="font-medium text-gray-800 mb-2">Créneau du
                                            {{ $timeSlot->start_time->format('d/m/Y H:i') }}</h5>
                                        <p class="text-sm text-gray-600 mb-1">
                                            {{ $timeSlot->start_time->format('d/m/Y H:i') }} -
                                            {{ $timeSlot->end_time->format('H:i') }}</p>
                                        <p class="text-sm text-gray-600">{{ $timeSlot->questions->count() }} questions
                                        </p>

                                        <div class="mt-4">
                                            <a href="#" class="text-sm text-indigo-600 hover:text-indigo-900">
                                                Voir toutes les questions
                                            </a>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
