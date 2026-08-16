<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserAccessTest extends TestCase
{
    use RefreshDatabase;

    private $admin;
    private $porteiro;
    private $fiscal;

    /**
     * Configura os utilizadores de teste antes de cada teste.
     */
    protected function setUp(): void
    {
        parent::setUp();

        // Criar utilizadores usando as nossas novas states da factory
        $this->admin = User::factory()->admin()->create();
        $this->porteiro = User::factory()->porteiro()->create();
        $this->fiscal = User::factory()->fiscal()->create();
    }

    /**
     * Teste 1: Visitantes não logados (guests) devem ser redirecionados para o login.
     */
    public function test_guest_is_redirected_to_login(): void
    {
        // Lista de rotas protegidas
        $protectedRoutes = [
            '/dashboard',
            '/users',
            '/vehicles',
            '/drivers',
            '/reports',
            '/fleet',
            '/aprovacao-relatorios',
          
        ];

        foreach ($protectedRoutes as $route) {
            $response = $this->get($route);
            // Verifica se foi redirecionado para a rota de login
            $response->assertRedirect('/login');
        }
    }

    /**
     * Teste 2: Testa as permissões do perfil 'porteiro'.
     */
    public function test_porteiro_access_permissions(): void
    {
        // Atua como o utilizador 'porteiro'
        $response = $this->actingAs($this->porteiro);

        // Rotas PERMITIDAS para porteiro
        $this->get('/dashboard')->assertStatus(200);
        $this->get('/fleet')->assertStatus(200);
        $this->get('/vehicles')->assertStatus(200);
        $this->get('/drivers')->assertStatus(200);
        $this->get('/reports')->assertStatus(200);
       // $this->get('/meus-relatorios')->assertStatus(200);
        $this->get('/relatorios/status')->assertStatus(200);

        // Rotas PROIBIDAS para porteiro (devem retornar 403 Forbidden)
        $this->get('/users')->assertStatus(403); // Apenas admin
        $this->get('/aprovacao-relatorios')->assertStatus(403); // Apenas admin, fiscal
    }

    /**
     * Teste 3: Testa as permissões do perfil 'fiscal'.
     */
    public function test_fiscal_access_permissions(): void
    {
        // Atua como o utilizador 'fiscal'
        $response = $this->actingAs($this->fiscal);

        // Rotas PERMITIDAS para fiscal
        $this->get('/dashboard')->assertStatus(200);
        $this->get('/vehicles')->assertStatus(200);
        $this->get('/drivers')->assertStatus(200);
        $this->get('/reports')->assertStatus(200);
        $this->get('/aprovacao-relatorios')->assertStatus(200);
        $this->get('/relatorios/status')->assertStatus(200);

        // Rotas PROIBIDAS para fiscal (devem retornar 403 Forbidden)
        $this->get('/users')->assertStatus(403); // Apenas admin
        $this->get('/fleet')->assertStatus(403);// Qualquer utilizador autenticado pode aceder
      //  $this->get('/meus-relatorios')->assertStatus(403); // Apenas porteiro
    }

    /**
     * Teste 4: Testa as permissões do perfil 'admin'.
     */
    public function test_admin_can_access_all_pages(): void
    {
        // Atua como o utilizador 'admin'
        $response = $this->actingAs($this->admin);

        // Admin pode aceder a TODAS as rotas principais
        $this->get('/dashboard')->assertStatus(200);
        $this->get('/users')->assertStatus(200); // Rota de admin
        $this->get('/vehicles')->assertStatus(200);
        $this->get('/drivers')->assertStatus(200);
        $this->get('/reports')->assertStatus(200);
        $this->get('/aprovacao-relatorios')->assertStatus(200); // Rota de fiscal/admin
        $this->get('/relatorios/status')->assertStatus(200);
        
        // Rotas de porteiro (Admin pode aceder?)
        // Assumindo que o admin pode aceder a tudo, mesmo rotas específicas de porteiro
        $this->get('/fleet')->assertStatus(200); 
       // $this->get('/meus-relatorios')->assertStatus(200);
    }
}