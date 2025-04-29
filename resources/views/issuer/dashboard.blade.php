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
                            <p class="mt-1 text-sm text-gray-500">{{ $user->position }} - {{ $organization->name }}</p>
                        </div>
                    </div>

                    <h4 class="text-md font-medium text-gray-700 mt-6">Vos réunions planifiées</h4>

                    @if ($meetingsByDate->isEmpty())
                        <p class="text-sm text-gray-500 mt-2">Vous n'avez aucune réunion planifiée.</p>
                    @else
                        @foreach ($meetingsByDate as $date => $meetings)
                            <div class="mt-4">
                                <h5 class="text-sm font-medium text-gray-700">{{ \Carbon\Carbon::parse($date)->translatedFormat('l d F Y') }}</h5>

                                <div class="mt-2 overflow-hidden shadow ring-1 ring-black ring-opacity-5 md:rounded-lg">
                                    <table class="min-w-full divide-y divide-gray-300">
                                        <thead class="bg-gray-50">
                                            <tr>
                                                <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900">Heure</th>
                                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Titre</th>
                                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Investisseurs</th>
                                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Salle</th>
                                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Questions</th>
                                                <th scope="col" class="relative py-3.5 pl-3 pr-4 text-right">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-200 bg-white">
                                            @foreach ($meetings as $meeting)
                                                @php
                                                    $investors = $meeting->investors();
                                                @endphp
                                                <tr>
                                                    <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm text-gray-900">
                                                        {{ $meeting->start_time->format('H:i') }}
                                                    </td>
                                                    <td class="whitespace-nowrap px-3 py-4 text-sm font-medium text-gray-900">
                                                        {{ $meeting->title }}
                                                    </td>
                                                    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                                        {{ $investors->count() }} investisseur(s)
                                                    </td>
                                                    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                                        {{ $meeting->room->name }}
                                                    </td>
                                                    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                                        {{ $meeting->questions->count() }}
                                                    </td>
                                                    <td class="relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium">
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
                        // Récupérer toutes les questions pour les réunions de cet émetteur
                        $recentQuestions = collect();
                        foreach ($meetings as $meeting) {
                            $recentQuestions = $recentQuestions->merge($meeting->questions);
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
                                        <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900">Date</th>
                                        <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Réunion</th>
                                        <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Investisseur</th>
                                        <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Question</th>
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
                                                    {{ $question->meeting->title }}
                                                </a>
                                            </td>
                                            <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                                {{ $question->user->name }} ({{ $question->user->organization->name }})
                                            </td>
                                            <td class="px-3 py-4 text-sm text-gray-500">
                                                {{ $question->content }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif

                    @if ($meetings->isNotEmpty())
                        <div class="mt-6">
                            <h4 class="text-md font-medium text-gray-700">Répartition des questions par réunion</h4>
                            <div class="mt-4 grid gap-4 grid-cols-1 md:grid-cols-2">
                                @foreach ($meetings->take(4) as $meeting)
                                    <div class="bg-white p-4 rounded-lg shadow">
                                        <h5 class="font-medium text-gray-800 mb-2">{{ $meeting->title }}</h5>
                                        <p class="text-sm text-gray-600 mb-1">{{ $meeting->start_time->format('d/m/Y H:i') }}</p>
                                        <p class="text-sm text-gray-600">{{ $meeting->questions->count() }} questions</p>

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
