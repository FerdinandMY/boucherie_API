<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Fournisseur;
use App\Models\User;
use Illuminate\Console\Command;

class FixFournisseursSansEntite extends Command
{
    protected $signature   = 'fournisseurs:fix-entites {--dry-run : Affiche les comptes concernés sans effectuer de corrections}';
    protected $description = 'Crée l\'entité Fournisseur manquante pour les utilisateurs ayant le rôle fournisseur';

    public function handle(): int
    {
        $fournisseurs = User::role('fournisseur')
            ->whereDoesntHave('fournisseur')
            ->get(['id', 'name', 'email']);

        if ($fournisseurs->isEmpty()) {
            $this->info('✓ Aucun compte fournisseur sans entité trouvé.');
            return self::SUCCESS;
        }

        $this->warn("Comptes fournisseur sans entité : {$fournisseurs->count()}");
        $this->table(['ID', 'Nom', 'Email'], $fournisseurs->map->only(['id', 'name', 'email']));

        if ($this->option('dry-run')) {
            $this->line('Mode dry-run : aucune modification effectuée.');
            return self::SUCCESS;
        }

        $created = 0;
        foreach ($fournisseurs as $user) {
            Fournisseur::create([
                'user_id'      => $user->id,
                'boucherie_id' => null,
                'nom'          => $user->name,
                'email'        => $user->email,
                'contact'      => null,
                'telephone'    => null,
                'adresse'      => null,
            ]);
            $this->line("  ✓ Entité créée pour {$user->email}");
            $created++;
        }

        $this->info("✓ {$created} entité(s) créée(s) avec succès.");
        return self::SUCCESS;
    }
}
