<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Admin Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Statistiques générales</h3>

                    <div class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                        <div class="bg-blue-100 overflow-hidden shadow rounded-lg">
                            <div class="px-4 py-5 sm:p-6">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Utilisateurs totaux</dt>
                                    <dd class="mt-1 text-3xl font-semibold text-gray-900">{{ $stats['totalUsers'] }}</dd>
                                </dl>
                            </div>
                        </div>

                        <div class="bg-green-100 overflow-hidden shadow rounded-lg">
                            <div class="px-4 py-5 sm:p-6">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Organisations</dt>
                                    <dd class="mt-1 text-3xl font-semibold text-gray-900">{{ $stats['totalOrganizations'] }}</dd>
                                </dl>
                            </div>
                        </div>

                        <div class="bg-yellow-100 overflow-hidden shadow rounded-lg">
                            <div class="px-4 py-5 sm:p-6">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Émetteurs</dt>
                                    <dd class="mt-1 text-3xl font-semibold text-gray-900">{{ $stats['issuers'] }}</dd>
                                </dl>
                            </div>
                        </div>

                        <div class="bg-indigo-100 overflow-hidden shadow rounded-lg">
                            <div class="px-4 py-5 sm:p-6">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Investisseurs</dt>
                                    <dd class="mt-1 text-3xl font-semibold text-gray-900">{{ $stats['investors'] }}</dd>
                                </dl>
                            </div>
                        </div>

                        <div class="bg-purple-100 overflow-hidden shadow rounded-lg">
                            <div class="px-4 py-5 sm:p-6">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Réunions</dt>
                                    <dd class="mt-1 text-3xl font-semibold text-gray-900">{{ $stats['meetings'] }}</dd>
                                </dl>
                            </div>
                        </div>

                        <div class="bg-pink-100 overflow-hidden shadow rounded-lg">
                            <div class="px-4 py-5 sm:p-6">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Salles</dt>
                                    <dd class="mt-1 text-3xl font-semibold text-gray-900">{{ $stats['rooms'] }}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>

                    <h3 class="mt-8 text-lg font-medium text-gray-900">Réunions récentes</h3>

                    <div class="mt-4 flex flex-col">
                        <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                            <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
                                <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
                                    <table class="min-w-full divide-y divide-gray-200">
                                        <thead class="bg-gray-50">
                                            <tr>
                                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Titre</th>
                                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Salle</th>
                                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Participants</th>
                                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-gray-200">
                                            @forelse ($recentMeetings as $meeting)
                                                <tr>
                                                    <td class="px-6 py-4 whitespace-nowrap">
                                                        <div class="text-sm font-medium text-gray-900">{{ $meeting->title }}</div>
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap">
                                                        <div class="text-sm text-gray-900">{{ $meeting->start_time->format('d/m/Y H:i') }}</div>
                                                        <div class="text-sm text-gray-500">{{ $meeting->duration_minutes }} min</div>
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                        {{ $meeting->room->name }}
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                        {{ $meeting->users->count() }}
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                        <a href="#" class="text-indigo-600 hover:text-indigo-900">Voir</a>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="5" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                                        Aucune réunion récente.
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4 text-right">
                        <a href="#" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-indigo-700 bg-indigo-100 hover:bg-indigo-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Voir toutes les réunions
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
