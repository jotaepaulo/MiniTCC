<?php
session_start();

if (!isset($_SESSION['logado']) || $_SESSION['tipo_conta'] !== 'cliente') {
    header("Location: pedidos.php");
    exit;
}

include "../processos/config.php";

$cliente_id = $_SESSION['id'];
/* Busca dados*/
$sqlCliente = "SELECT nome, foto FROM cliente WHERE id = $cliente_id";
$resCliente = $conn->query($sqlCliente);
$cliente = $resCliente->fetch_assoc();

$fotoPerfil = $cliente['foto'] ?? 'user.png';



// Puxa todas as pizzas do banco
$sql = "SELECT 
    pizza.id,
    pizza.nome,
    pizza.descricao,
    pizza.imagem,
    pizza_preco.preco
FROM pizza
JOIN pizza_preco ON pizza.id = pizza_preco.pizza_id
JOIN tamanho_pizza ON pizza_preco.tamanho_id = tamanho_pizza.id
WHERE tamanho_pizza.nome = 'media'
ORDER BY pizza.nome";

$resultado = $conn->query($sql);

// Armazenar todas as pizzas
$todas_pizzas = [];
if ($resultado->num_rows > 0) {
    while ($row = $resultado->fetch_assoc()) {
        $todas_pizzas[] = $row;
    }
}

// Organizar em slides
$pizzas_por_slide = 6;
$total_slides = ceil(count($todas_pizzas) / $pizzas_por_slide);

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'finalizar_pedido') {
    $response = processarPedido($conn, $_SESSION['id']);
    echo json_encode($response);
    exit;
}

// Função para processar o pedido
function processarPedido($conn, $cliente_id) {
  
    $carrinho = json_decode($_POST['carrinho'], true);
    $endereco = $_POST['endereco'];
    $observacoes = $_POST['observacoes'] ?? '';
    
    if (empty($carrinho)) {
        return ['success' => false, 'message' => 'Carrinho vazio'];
    }
    
    try {
       
        $total = 0;
        foreach ($carrinho as $item) {
            $total += $item['preco'] * $item['quantidade'];
        }
        
       
        $sql_pedido = "INSERT INTO pedido (cliente_id, total, status) VALUES (?, ?, 'pendente')";
        $stmt_pedido = $conn->prepare($sql_pedido);
        $stmt_pedido->bind_param("id", $cliente_id, $total);
        
        if ($stmt_pedido->execute()) {
            $pedido_id = $stmt_pedido->insert_id;
            
            $sql_item = "INSERT INTO item_pedido (pedido_id, pizza_id, tamanho_id, quantidade) VALUES (?, ?, ?, ?)";
            $stmt_item = $conn->prepare($sql_item);
       
            $tamanhos_ids = [
                'pequena' => 1,
                'media' => 2,
                'grande' => 3,
                'familia' => 4
            ];
            
            foreach ($carrinho as $item) {
                $tamanho_id = $tamanhos_ids[$item['tamanho']];
                $stmt_item->bind_param("iiii", $pedido_id, $item['id'], $tamanho_id, $item['quantidade']);
                $stmt_item->execute();
            }
            
           
            
            
            return [
                'success' => true, 
                'message' => 'Pedido realizado com sucesso!', 
                'pedido_id' => $pedido_id,
                'total' => $total
            ];
        } else {
            return ['success' => false, 'message' => 'Erro ao criar pedido'];
        }
        
    } catch (Exception $e) {
        return ['success' => false, 'message' => 'Erro: ' . $e->getMessage()];
    }
}
?>
<!DOCTYPE html>
<html lang="pt">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Pizzas</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

  <link rel="stylesheet" href="../css/stylePedidos.css">

</head>

<script>

window.addEventListener('beforeunload', function(event) {
   
    const currentUrl = window.location.href;
    
  
    if (performance.navigation.type === 1) {
     
        return;
    }
    
   
    localStorage.removeItem('carrinho_pizzaria');
});


function limparCarrinhoLogout() {
    localStorage.removeItem('carrinho_pizzaria');
    window.location.href = '../processos/logout.php';
}

document.querySelector('a[href="../processos/logout.php"]').addEventListener('click', function(e) {
    e.preventDefault();
    limparCarrinhoLogout();
});


async function enviarPedido() {
   
    
    if (result.success) {
       
        cart = [];
        localStorage.removeItem('carrinho_pizzaria');
        updateCartUI();
        
        
        await fetch('limpar_carrinho.php');
        
    }
}
</script>

<body>

  <header>

  
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg bg-body-tertiary">
      <div class="container-fluid">
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent">
          <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarSupportedContent">
          <a href="#">
            <img class="logo" src="../img/LOGO.png" alt="logo" />
          </a>
          <a href="principal.php" class="btn-pedidos">Voltar</a>
          
          <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
            <li class="nav-item dropdown">
               <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
            <img class="user"  src="../img/clientes/<?= htmlspecialchars($fotoPerfil); ?>" alt="logo" style="width: 38px; height: 38px;">
        </a>
              <ul class="dropdown-menu dropdown-menu-end" style="background-color: black;">
            <li><a class="dropdown-item text-white" href="../paginas/perfil.php">Meu Perfil</a></li>
                <li><a class="dropdown-item text-white" href="../processos/logout.php">Sair</a></li>
              </ul>
            </li>
          </ul>
        </div>
      </div>
    </nav>


  </header>

 <div class="pizza-container"></div>
<?php if (count($todas_pizzas) > 0): ?>
<div class="pizza-carousel-wrapper">
  <button class="pc-prev" onclick="prevSlide()">❮</button>
  
  <div class="pizza-carousel-slides" id="pizzaSlides">
    <?php for ($slide = 0; $slide < $total_slides; $slide++): 
      $pizzas_no_slide = array_slice($todas_pizzas, $slide * $pizzas_por_slide, $pizzas_por_slide);
    ?>
    <!-- SLIDE <?= $slide + 1 ?> -->
    <div class="pizza-slide">
      <?php foreach ($pizzas_no_slide as $pizza): ?>
      <div class="pizza-card">
        <img src="../img/pizzas/<?= htmlspecialchars($pizza['imagem']) ?>" 
             alt="<?= htmlspecialchars($pizza['nome']) ?>">
        <h3><?= htmlspecialchars($pizza['nome']) ?></h3>
        <p class="card-price">R$ <?= number_format($pizza['preco'], 2, ',', '.') ?></p>
        <button class="btn btn-buy" 
                onclick="openBuyModal(
                  <?= $pizza['id'] ?>,
                  '<?= addslashes($pizza['nome']) ?>',
                  '<?= addslashes($pizza['descricao']) ?>',
                  '<?= addslashes($pizza['imagem']) ?>',
                  <?= $pizza['preco'] ?>
                )">Comprar agora</button>
      </div>
      <?php endforeach; ?>
    </div>
    <?php endfor; ?>
  </div>
  
  <button class="pc-next" onclick="nextSlide()">❯</button>
</div>


<div class="carousel-indicators" id="carouselIndicators">
  <?php for ($i = 0; $i < $total_slides; $i++): ?>
    <span class="carousel-dot <?= $i === 0 ? 'active' : '' ?>" 
          onclick="goToSlide(<?= $i ?>)"></span>
  <?php endfor; ?>
</div>

<?php else: ?>
  <div class="text-center py-5">
    <h3>Nenhuma pizza disponível no momento.</h3>
  </div>
<?php endif; ?>


<div class="modal" id="buyModal">
    <div class="modal-content">
        <span class="close" onclick="closeModal()">&times;</span>
        <img id="buyImg" src="" alt="Pizza">
        <h2 id="buyTitle">Título da Pizza</h2>
        <p id="buyPrice">R$ 00,00</p>
        

        <div id="buyDesc" class="pizza-descricao"></div>

        <div class="modal-section">
            <div class="section-header">
                Tamanho
                <small>Escolha 1</small>
            </div>
            
            <div id="sizeOptions"></div>
        </div>
<div class="modal-section">
    <div class="section-header">
        Quantidade
    </div>

    <div class="quantity-selector">
        <button onclick="changeQuantity(-1)">−</button>
        <input type="number" id="quantity" value="1" min="1" max="10">
        <button onclick="changeQuantity(1)">+</button>
    </div>
</div>

        
        <input type="hidden" id="selectedPizzaId">

        <button class="modal-btn" onclick="confirmAddToCart()">Adicionar ao Carrinho</button>
    </div>
</div>


<div class="modal modal-endereco" id="enderecoModal">
    <div class="modal-content">
        <span class="close" onclick="closeEnderecoModal()">&times;</span>
        <h3>Endereço de Entrega</h3>
        
        <div style="margin: 20px 0;">
            <label style="display: block; margin-bottom: 8px; font-weight: bold;">
                Digite seu endereço:
            </label>
            <input type="text" id="endereco_simples" 
                   style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px;"
                   placeholder="Ex: Rua das Flores, 123">
        </div>
        
        <button type="button" id="btnFinalizarPedido"
                style="background: #23ad5d; color: white; border: none; padding: 12px 20px; 
                       border-radius: 5px; width: 100%; font-size: 16px; cursor: pointer;"
                onclick="enviarPedido()">
                Finalizar Pedido
        </button>
    </div>
</div>
<!-- CARRINHO -->
<div class="cart">
  <h4>Seu Carrinho</h4>
  <div class="cart-items" id="cartItems"></div>

  <div class="cart-total">
    <span>Total:</span>
    <span id="cartTotal">R$ 0,00</span>
  </div>

  <button class="checkout-btn" onclick="abrirModalEndereco()">Finalizar Compra</button>
</div>


<script>
let currentSlide = 0;
const totalSlides = <?= $total_slides ?>;

function nextSlide() {
    currentSlide = (currentSlide + 1) % totalSlides;
    updateCarousel();
}

function prevSlide() {
    currentSlide = (currentSlide - 1 + totalSlides) % totalSlides;
    updateCarousel();
}

function goToSlide(slideIndex) {
    currentSlide = slideIndex;
    updateCarousel();
}

function updateCarousel() {
    const slides = document.getElementById("pizzaSlides");
    slides.style.transform = `translateX(-${currentSlide * 100}%)`;
    
    document.querySelectorAll('.carousel-dot').forEach((dot, index) => {
        dot.classList.toggle('active', index === currentSlide);
    });
}
</script>

<!--MODAL DE PIZZA -->
<script>
let basePrice = 0;
let finalPrice = 0;
let currentQuantity = 1;
let selectedPizzaId = 0;
let selectedPizzaData = {};

async function buscarPrecosPizza(pizzaId) {
    try {
        const response = await fetch(`../processos/get_precos_pizza.php?id=${pizzaId}`);
        const data = await response.json();
        return data.precos || {};
    } catch (error) {
        console.error('Erro ao buscar preços:', error);
        return {};
    }
}

async function openBuyModal(id, nome, descricao, imagem, precoMedio) {
    selectedPizzaId = id;
    selectedPizzaData = { id, nome, descricao, imagem };
    basePrice = precoMedio;
    finalPrice = precoMedio;
    currentQuantity = 1;
    
    document.getElementById("buyImg").src = `../img/pizzas/${imagem}`;
    document.getElementById("buyTitle").innerText = nome;
    document.getElementById("buyDesc").innerText = descricao;
    document.getElementById("buyPrice").innerText = "R$ " + precoMedio.toFixed(2).replace(".", ",");
    document.getElementById("selectedPizzaId").value = id;
    document.getElementById("quantity").value = 1;
    
    const precos = await buscarPrecosPizza(id);
    const sizeOptions = document.getElementById("sizeOptions");
    sizeOptions.innerHTML = '';
    
    const tamanhos = [
      
        { nome: 'media', label: 'Média', fatias: '6 fatias' },
        { nome: 'grande', label: 'Grande', fatias: '8 fatias' },
    ];
    
    tamanhos.forEach(tamanho => {
        if (precos[tamanho.nome]) {
            const label = document.createElement('label');
            label.className = 'modal-option';
            label.innerHTML = `
                <div>
                    <strong>${tamanho.label}</strong>
                    <p>${tamanho.fatias}</p>
                </div>
                <div class="option-right">
                    <input type="radio" name="size" value="${precos[tamanho.nome]}" 
                           data-tamanho="${tamanho.nome}">
                    <span class="option-price">R$ ${precos[tamanho.nome].toFixed(2).replace('.', ',')}</span>
                </div>
            `;
            sizeOptions.appendChild(label);
            
            if (tamanho.nome === 'media') {
                label.querySelector('input').checked = true;
            }
            
            label.querySelector('input').addEventListener('change', function() {
                basePrice = parseFloat(this.value);
                updateModalPrice();
            });
        }
    });
    
    document.getElementById("buyModal").style.display = "flex";
}

function changeQuantity(delta) {
    currentQuantity = Math.max(1, currentQuantity + delta);
    document.getElementById("quantity").value = currentQuantity;
    updateModalPrice();
}

function updateModalPrice() {
    finalPrice = basePrice * currentQuantity;
    document.getElementById("buyPrice").innerText = "R$ " + finalPrice.toFixed(2).replace(".", ",");
}

function closeModal() {
    document.getElementById("buyModal").style.display = "none";
}

function confirmAddToCart() {
    const selectedSize = document.querySelector('input[name="size"]:checked');
    if (!selectedSize) {
        alert('Selecione um tamanho!');
        return;
    }
    
    const tamanho = selectedSize.dataset.tamanho;
    const preco = parseFloat(selectedSize.value);
    
    const item = {
        id: selectedPizzaId,
        nome: selectedPizzaData.nome,
        tamanho: tamanho,
        preco: preco,
        quantidade: currentQuantity,
        imagem: selectedPizzaData.imagem
    };
    
    addToCart(item);
    closeModal();
}
</script>

<!--CARRINHO E FINALIZAÇÃO -->
<script>
let cart = JSON.parse(localStorage.getItem('carrinho_pizzaria')) || [];

function addToCart(item) {
    const existingItemIndex = cart.findIndex(cartItem => 
        cartItem.id === item.id && cartItem.tamanho === item.tamanho);
    
    if (existingItemIndex !== -1) {
        cart[existingItemIndex].quantidade += item.quantidade;
    } else {
        cart.push(item);
    }
    
    localStorage.setItem('carrinho_pizzaria', JSON.stringify(cart));
    updateCartUI();
    alert('Item adicionado ao carrinho!');
}

function updateCartUI() {
    const list = document.getElementById("cartItems");
    const totalEl = document.getElementById("cartTotal");

    list.innerHTML = "";
    let total = 0;

    cart.forEach((item, index) => {
        const itemTotal = item.preco * item.quantidade;
        total += itemTotal;

        const div = document.createElement("div");
        div.className = "cart-item";
        div.innerHTML = `
            <div>
                <strong>${item.nome}</strong><br>
                <small>Tamanho: ${item.tamanho.charAt(0).toUpperCase() + item.tamanho.slice(1)}</small><br>
                <small>Qtd: ${item.quantidade} × R$ ${item.preco.toFixed(2).replace(".", ",")}</small>
            </div>
            <div>
                <div>R$ ${itemTotal.toFixed(2).replace(".", ",")}</div>
                <button class="remove-item" onclick="removerDoCarrinho(${index})">×</button>
            </div>
        `;
        list.appendChild(div);
    });

    totalEl.innerText = `R$ ${total.toFixed(2).replace(".", ",")}`;
}

function removerDoCarrinho(index) {
    if (confirm('Remover item do carrinho?')) {
        cart.splice(index, 1);
        localStorage.setItem('carrinho_pizzaria', JSON.stringify(cart));
        updateCartUI();
    }
}

// Modal de endereço
function abrirModalEndereco() {
    if (cart.length === 0) {
        alert('Seu carrinho está vazio!');
        return;
    }
    
    document.getElementById("enderecoModal").style.display = "flex";
}

function closeEnderecoModal() {
    document.getElementById("enderecoModal").style.display = "none";
}
async function enviarPedido() {
    // Validar endereço
    const enderecoInput = document.getElementById('endereco_simples');
    const endereco = enderecoInput.value.trim();
    
    if (!endereco) {
        alert('Por favor, informe o endereço de entrega!');
        return;
    }
    
    if (cart.length === 0) {
        alert('Seu carrinho está vazio!');
        return;
    }
    
    
    alert('Processando seu pedido...');
    
    try {
        // Montar dados
        const dados = new URLSearchParams();
        dados.append('action', 'finalizar_pedido');
        dados.append('carrinho', JSON.stringify(cart));
        dados.append('endereco', endereco);
        
        // Enviar pedido
        const response = await fetch('../processos/processar_pedido.php', {
            method: 'POST',
            body: dados
        });
        
        const result = await response.json();
        
        if (result.success) {
           
            cart = [];
            localStorage.removeItem('carrinho_pizzaria');
            updateCartUI();
            
            // Fechar modal
            closeEnderecoModal();
            
            // Mostrar sucesso
            alert(` Pedido #${result.pedido_id} realizado!\nTotal: R$ ${result.total}\nObrigado!`);
        } else {
            alert('❌ Erro: ' + result.message);
        }
        
    } catch (error) {
        console.error('Erro:', error);
        alert('❌ Erro ao processar. Verifique sua conexão.');
    }
}
// Fechar modais ao clicar fora
document.getElementById("buyModal").addEventListener("click", function(e) {
    if (e.target === this) closeModal();
});

document.getElementById("enderecoModal").addEventListener("click", function(e) {
    if (e.target === this) closeEnderecoModal();
});

document.addEventListener('DOMContentLoaded', function() {
    updateCarousel();
    updateCartUI();
});
</script>

</body>
</html>