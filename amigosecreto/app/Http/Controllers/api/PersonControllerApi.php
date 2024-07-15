<?php

namespace App\Http\Controllers\api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log; 
use Illuminate\Support\Facades\DB;
use App\Models\Person;
use App\Models\Pair;
use App\Http\Controllers\Controller;

class PersonControllerApi extends Controller
{
    public function index()
    {
        $people = Person::all();

        if ($people->isEmpty()) {
            return response()->json(['error' => 'Nenhuma pessoa encontrada.'], 404);
        }

        return response()->json($people, 200);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255|unique:people',
            'email' => 'required|string|email|max:255|unique:people',
        ], [
            'name.unique' => 'Este nome já está cadastrado.',
            'email.unique' => 'Este email já está cadastrado.',
        ]);
    
        try {
            $person = Person::create($validatedData);
    
            Log::info('Pessoa cadastrada com sucesso.', ['person_id' => $person->id]);
    
            return response()->json($person, 201);
        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->errorInfo[1] == 1062) { 
                Log::error('Erro ao cadastrar pessoa: Duplicação de chave.', ['error' => $e->getMessage()]);
                return response()->json(['error' => 'Já existe um registro com este nome ou email.'], 422);
            } else {
                Log::error('Erro ao cadastrar pessoa.', ['error' => $e->getMessage()]);
                return response()->json(['error' => 'Erro ao cadastrar pessoa.'], 500);
            }
        } catch (\Exception $e) {
            Log::error('Erro ao cadastrar pessoa.', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Erro ao cadastrar pessoa.'], 500);
        }
    }
    

    public function show($id)
    {
        Log::info('Buscando pessoa com ID: ' . $id);

        $person = Person::find($id);

        if (is_null($person)) {
            Log::warning('Pessoa não encontrada com ID: ' . $id);
            return response()->json(['message' => 'Pessoa não encontrada'], 404);
        }

        Log::info('Pessoa recuperada com sucesso.', ['person_id' => $person->id]);

        return response()->json($person, 200);
    }

    public function update(Request $request, $id)
    {
        $person = Person::find($id);

        if (is_null($person)) {
            Log::warning('Pessoa não encontrada com ID: ' . $id);
            return response()->json(['message' => 'Pessoa não encontrada'], 404);
        }

        $validatedData = $request->validate([
            'name' => 'required|string|max:255|unique:people,name,' . $person->id,
            'email' => 'required|string|email|max:255|unique:people,email,' . $person->id,
        ]);

        try {
            $person->update($validatedData);
            Log::info('Pessoa atualizada com sucesso.', ['person_id' => $person->id]);
            return response()->json($person, 200);
        } catch (\Exception $e) {
            Log::error('Erro ao atualizar pessoa: ' . $e->getMessage());
            return response()->json(['message' => 'Erro ao atualizar pessoa'], 500);
        }
    }

    
    
    public function destroy($id)
    {
        Log::info('caiu dentro do controller da api metodo destroy: ' . $id);

        $person = Person::find($id);

        if (is_null($person)) {
            Log::warning('Pessoa não encontrada com ID: ' . $id);
            return response()->json(['message' => 'Pessoa não encontrada'], 404);
        }

        $person->delete();

        Log::info('Pessoa excluída com sucesso.', ['person_id' => $id]);

        return response()->json(['message' => 'Pessoa excluída'], 200);
    }

    public function raffle()
    {
        Log::info('Iniciando sorteio...');

        // Obter todas as pessoas cadastradas
        $people = Person::all();
        Log::info('Pessoas obtidas:', $people->toArray());

        // Verificar se há pelo menos duas pessoas para o sorteio
        if ($people->count() < 2) {
            Log::error('Menos de duas pessoas para o sorteio.');
            return response()->json(['error' => 'É necessário pelo menos 2 pessoas para o sorteio.'], 400);
        }

        // Embaralhar a lista de pessoas
        $shuffledPeople = $people->shuffle();
        Log::info('Pessoas embaralhadas:', $shuffledPeople->toArray());

        // Apagar todos os pares anteriores (se necessário)
        Pair::truncate();
        Log::info('Pares anteriores apagados.');

        // Criar pares para o sorteio
        $pairs = [];
        for ($i = 0; $i < $shuffledPeople->count(); $i++) {
            $giver = $shuffledPeople[$i];
            $receiver = $shuffledPeople[($i + 1) % $shuffledPeople->count()];

            // Criar um novo par no banco de dados
            $pair = Pair::create([
                'giver_id' => $giver->id,
                'receiver_id' => $receiver->id,
            ]);

            $pairs[] = $pair;
        }

        Log::info('Pares criados:', $pairs);

        // Retornar os pares como JSON
        return response()->json($pairs, 200);
    }

    public function showPair($id)
    {
        $pair = Pair::where('giver_id', $id)->first();

        if (!$pair) {
            return response()->json(['error' => 'Par não encontrado.'], 404);
        }

        return response()->json($pair->load('receiver'), 200);
    }
}

