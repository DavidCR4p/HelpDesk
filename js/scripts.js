document.addEventListener('DOMContentLoaded', function() {
    let ticketIdToRemove = null;

    // Função para exibir o modal de remoção
    function remover(ticketId) {
        ticketIdToRemove = ticketId; // Armazena o ID do ticket a ser removido
        const modal = document.getElementById('confirmModal');
        modal.style.display = 'block';
    }

    // Função para fechar o modal
    function closeModal() {
        document.getElementById('confirmModal').style.display = 'none';
    }

    // Quando o botão "Sim" é clicado no modal de confirmação
    document.getElementById('confirmBtn').addEventListener('click', function() {
        if (ticketIdToRemove !== null) {
            // Realiza a requisição para remover o ticket
            fetch('remover_ticket.php?id=' + ticketIdToRemove, {
                method: 'GET'
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Erro na resposta do servidor: ' + response.statusText);
                }
                return response.json(); // Espera a resposta no formato JSON
            })
            .then(data => {
                closeModal(); // Fechar modal

                if (data.status === 'success') {
                    alert('Ticket removido com sucesso!');
                    location.reload(); // Recarregar a página após remoção
                } else {
                    alert('Erro ao remover o ticket: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Erro ao remover o ticket:', error);
                alert('Ocorreu um erro ao tentar remover o ticket: ' + error.message);
            });
        } else {
            closeModal(); // Fechar modal se não houver ID
        }
    });

    // Adicionando evento de fechamento do modal ao botão "Não"
    document.querySelector('button[onclick="closeModal()"]').addEventListener('click', closeModal);
});

function copiar(ticketId) {
    const xhr = new XMLHttpRequest();
    xhr.open('POST', 'assumir_ticket.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.onreadystatechange = function() {
        if (xhr.readyState === 4 && xhr.status === 200) {
            const response = JSON.parse(xhr.responseText);
            if (response.status === 'success') {
                // Atualiza o campo de assignee (quem assumiu) diretamente na tabela
                const assigneeCell = document.querySelector(`#ticket-${ticketId} .assignee-cell`);
                assigneeCell.textContent = response.assignee;
            } else {
                alert('Erro ao assumir o ticket: ' + response.message);
            }
        }
    };
    xhr.send('id=' + ticketId);
}


// Função para editar um ticket
function editar(ticketId) {
    window.location.href = 'edit_ticket.php?id=' + ticketId;
}

function openEditPopup(id) {
    document.getElementById("editPopupOverlay").style.display = "flex";
    
    // Buscar os dados do usuário no banco de dados
    $.ajax({
      type: "GET",
      url: "buscar_usuario.php",
      data: {id: id},
      success: function(data) {
        var usuario = JSON.parse(data);
        
        // Preencher os campos do formulário de edição com os dados do usuário
        document.getElementById("editUserId").value = usuario.id;
        document.getElementById("editUserName").value = usuario.name;
        document.getElementById("editUserEmail").value = usuario.email;
        document.getElementById("editUserTipoUsuario").value = usuario.tipo_usuario;
      }
    });
  }

  function openEditPopup(id) {
    document.getElementById("editPopupOverlay").style.display = "flex";
    // Aqui você pode adicionar código para preencher os campos do formulário de edição com os dados do usuário
    document.getElementById("editUserId").value = id;
  }

  function openDropdown(event, dropdownID) {
    var dropdown = document.getElementById(dropdownID);
    dropdown.classList.toggle("show");
  }
  
  window.onclick = function(event) {
    if (!event.target.matches('.dropdown-btn')) {
      var dropdowns = document.getElementsByClassName("dropdown-content");
      var i;
      for (i = 0; i < dropdowns.length; i++) {
        var openDropdown = dropdowns[i];
        if (openDropdown.classList.contains('show')) {
          openDropdown.classList.remove('show');
        }
      }
    }
  }

  function openDropdown(event, dropdownID) {
    var dropdown = document.getElementById(dropdownID);
    dropdown.style.display = "block";
  }
  
  function closeDropdown(event, dropdownID) {
    var dropdown = document.getElementById(dropdownID);
    dropdown.style.display = "none";
  }

  function removerUsuario(id) {
    if (confirm('Tem certeza que deseja deletar este usuário?')) {
      window.location.href = 'deletar_usuario.php?id=' + id;
    }
  }

  document.getElementById('filter-btn').addEventListener('click', function() {
    var filterValue = document.getElementById('filter-input').value;
    window.location.href = 'configuracao_usuario.php?filtro=' + filterValue;
});

function toggleFilters() {
  const filterContainer = document.getElementById('filterContainer');
  filterContainer.style.display = filterContainer.style.display === 'none' ? 'block' : 'none';
}

function visualizar(ticketId) {
  window.location.href = 'view_ticket.php?id=' + ticketId;
}