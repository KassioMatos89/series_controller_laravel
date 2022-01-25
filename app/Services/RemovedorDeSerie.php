<?php

namespace App\Services;

use Storage;
use App\Events\SerieApagada;
use Illuminate\Support\Facades\DB;
use App\{Serie, Temporada, Episodio};
use App\Jobs\ExcluirCapaSerie;

class RemovedorDeSerie
{
    public function removerSerie(int $serieId): string
    {
        $nomeSerie = '';
        DB::transaction(function () use ($serieId, &$nomeSerie) {
            $serie = Serie::find($serieId);
            $serieObj = (object) $serie->toArray();

            $nomeSerie = $serie->nome;

            $this->removerTemporadas($serie);
            $serie->delete();

            $evento = new SerieApagada($serieObj);
            event($evento);
            ExcluirCapaSerie::dispatch($serieObj);
        });

        return $nomeSerie;
    }

    /**
     * Remove Serie and its Temporadas
     *
     * @param Serie $serie
     * @return void
     */
    private function removerTemporadas(Serie $serie): void
    {
        $serie->temporadas->each(function (Temporada $temporada) {
            $this->removerEpisodios($temporada);
            $temporada->delete();
        });
    }

    /**
     * Remove Temporada
     *
     * @param Temporada $temporada
     * @return void
     */
    private function removerEpisodios(Temporada $temporada): void
    {
        $temporada->episodios->each(function (Episodio $episodio) {
            $episodio->delete();
        });
    }
}
