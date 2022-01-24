<?php

namespace App\Services;

use App\Serie;
use Illuminate\Support\Facades\DB;

class CriadorDeSerie
{
    public function criarSerie(
        string $nomeSerie,
        int $qtdTemporadas,
        int $epPorTemporada,
        ?string $capa
    ): Serie
    {
        // BeginTransaction Laravel
        DB::beginTransaction();
        $serie = Serie::create([
            'nome' => $nomeSerie,
            'capa' => $capa
        ]);
        $this->criarTemporadas($qtdTemporadas, $epPorTemporada, $serie);
        DB::commit();

        return $serie;
    }

    /**
     * Cria as temporadas de uma série
     *
     * @param integer $qtdTemporadas
     * @param integer $epPorTemporada
     * @param Serie $serie
     * @return void
     */
    private function criarTemporadas(int $qtdTemporadas, int $epPorTemporada, Serie $serie): void
    {
        for ($i = 1; $i <= $qtdTemporadas; $i++) {
            $temporada = $serie->temporadas()->create(['numero' => $i]);

            $this->criarEpisodios($epPorTemporada, $temporada);
        }
    }

    /**
     * Cria episodios de uma temporada
     *
     * @param integer $epPorTemporada
     * @param \Illuminate\Database\Eloquent\Model $temporada
     * @return void
     */
    private function criarEpisodios(int $epPorTemporada, \Illuminate\Database\Eloquent\Model $temporada): void
    {
        for ($j = 1; $j <= $epPorTemporada; $j++) {
            $temporada->episodios()->create(['numero' => $j]);
        }
    }
}
