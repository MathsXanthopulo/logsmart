<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Http;
use App\Models\Person; 
use App\Models\Pair;
use Illuminate\Support\Facades\Log;

class PersonController extends Controller
{
    public function index()
    {
        $people = Person::all();
        Log::info('caiu metodo index do controller web.');
        return view('home', compact('people'));
    }

    public function store(Request $request)
    {
        Log::info('caiu dentro do metodo store do controller da web.', $request->all());
    
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:people',
        ]);
        $apiUrl = route('api.store');

        $client = new Client();
    
        try {
            $response = $client->post($apiUrl, [
                'form_params' => [
                    'name' => $request->name,
                    'email' => $request->email,
                ],
            ]);
    
            if ($response->getStatusCode() == 200) {
                $responseBody = json_decode($response->getBody(), true);
                Log::info('Pessoa cadastrada com sucesso pela API.', $responseBody);
                return redirect()->route('home')->with('success', 'Pessoa cadastrada com sucesso!');
            } else {
                Log::error('Erro ao cadastrar pessoa pela API.', ['response' => $response->getBody()->getContents()]);
                return redirect()->route('home')->with('error', 'Erro ao cadastrar pessoa.');
            }
        } catch (\Exception $e) {
            Log::error('Erro ao cadastrar pessoa.', ['error' => $e->getMessage()]);
            return redirect()->route('home')->with('error', 'Erro ao cadastrar pessoa.');
        }
    }

    public function show($id)
{
    Log::info('Buscando pessoa com ID: ' . $id);

    // Aqui você deve buscar os dados da pessoa
    try {
        // Exemplo de busca de pessoa (substitua pelo seu método real)
        $person = Person::findOrFail($id);

        Log::info('Pessoa recuperada com sucesso.', ['person_id' => $person->id]);

        // Aqui você pode retornar os dados da pessoa como JSON para o JavaScript
        return response()->json($person);
    } catch (\Exception $e) {
        Log::error('Erro ao buscar pessoa.', ['error' => $e->getMessage()]);
        return response()->json(['error' => 'Erro ao buscar pessoa.'], 500);
    }
}
    


    public function update(Request $request, $id)
    {
        Log::info('Caiu no controller da api web update: ' . $id);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:people,email,' . $id,
        ]);

        $apiUrl = route('api.update', ['id' => $id]);

        $client = new Client();

        try {
            $response = $client->put($apiUrl, [
                'json' => [  // Passando os dados no formato JSON
                    'name' => $request->name,
                    'email' => $request->email,
                ],
            ]);

            if ($response->getStatusCode() == 200) {
                Log::info('Pessoa atualizada com sucesso pela API.', ['response' => $response->getBody()->getContents()]);
                return redirect()->route('home')->with('success', 'Pessoa atualizada com sucesso!');
            } else {
                Log::error('Erro ao atualizar pessoa pela API.', ['response' => $response->getBody()->getContents()]);
                return redirect()->route('home')->with('error', 'Erro ao atualizar pessoa.');
            }
        } catch (\Exception $e) {
            Log::error('Erro ao atualizar pessoa.', ['error' => $e->getMessage()]);
            return redirect()->route('home')->with('error', 'Erro ao atualizar pessoa.');
        }
    }


    public function destroy($id)
    {
        Log::info('caiu no controller da web metedo destroy: ' . $id);
    
        $apiUrl = route('api.destroy', ['id' => $id]);
    
        $client = new Client();
    
        try {
            $response = $client->delete($apiUrl, [
                'headers' => [
                    'X-CSRF-TOKEN' => csrf_token(),
                ],
            ]);
    
            if ($response->getStatusCode() == 200) {
                $responseBody = json_decode($response->getBody(), true);
                Log::info('Pessoa excluída com sucesso pela API.', $responseBody);
                return response()->json(['message' => 'Pessoa excluída com sucesso!'], 200);
            } else {
                $responseBody = $response->getBody()->getContents();
                Log::error('Erro ao excluir pessoa pela API.', ['response' => $responseBody]);
                return response()->json(['message' => 'Erro ao excluir pessoa.'], $response->getStatusCode());
            }
        } catch (\Exception $e) {
            Log::error('Erro ao excluir pessoa.', ['error' => $e->getMessage()]);
            return response()->json(['message' => 'Erro ao excluir pessoa.'], 500);
        }
    }

    public function raffles()
    {
        try {
            $apiUrl = route('api.raffle');
    
            Log::info('Caiu dentro do controller da web e mandando requisição para o controller api.', ['api_url' => $apiUrl]);
    
            $response = Http::get($apiUrl);
    
            $response->throw();
    
            $pairs = collect($response->json())->map(function ($pair) {
                return (object) $pair; // Convertendo cada par em objeto (opcional)
            });
    
            Log::info('Pares recebidos da API:', ['pairs' => $pairs]);
    
            // Carregar todas as pessoas (opcional, se já estiverem carregadas no modelo)
            $people = Person::all();
            $pairs = Pair::with('giver', 'receiver')->get();
            return view('raffle', compact('pairs'));
    
        } catch (\Exception $e) {
            Log::error('Erro ao tentar realizar o sorteio.', ['exception' => $e]);
    
            return redirect()->back()->with('error', 'Ocorreu um erro ao tentar realizar o sorteio.');
        }
    }
}

