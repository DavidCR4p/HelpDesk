<?php
include 'db.php';
include 'conexao.php';

$query = "
    SELECT id, subject, assignee, status, created_at, category, sector, urgency
    FROM tickets
    WHERE assignee IS NULL OR assignee = ''
    ORDER BY
        FIELD(urgency, 'urgente', 'alta', 'média', 'baixa'),
        created_at asc
";

$result = $conn->query($query);

while ($ticket = $result->fetch_assoc()) {
    echo "<tr id='ticket-" . $ticket['id'] . "'>";
    echo "<td>" . htmlspecialchars($ticket['id']) . "</td>";
    echo "<td>" . htmlspecialchars($ticket['subject']) . "</td>";
    echo "<td class='assignee-cell'>nenhum</td>";
    echo "<td data-status='" . getStatusText($ticket['status']) . "'>" . getStatusText($ticket['status']) . "</td>";
    echo "<td>" . date('d/m/Y H:i:s', strtotime($ticket['created_at'])) . "</td>";
    echo "<td>" . htmlspecialchars($ticket['category']) . "</td>";
    echo "<td>" . htmlspecialchars($ticket['sector']) . "</td>";
    echo "<td>" . htmlspecialchars($ticket['urgency']) . "</td>";
    echo "<td>";
    echo "<button onclick='editar(" . $ticket['id'] . ")'>Editar</button>";
    echo "<button onclick='copiar(" . $ticket['id'] . ")'>Assumir</button>";
    echo "<button onclick='remover(" . $ticket['id'] . ")'>Remover</button>";
    echo "<button onclick='visualizar(" . $ticket['id'] . ")'>Visualizar</button>";
    echo "</td>";
    echo "</tr>";
}

function getStatusText($statusNumber) {
    $statusMap = [
        '1' => 'Aberto',
        '2' => 'Atendimento',
        '3' => 'Fechado',
        '4' => 'Encerrado',
        '5' => 'Espera'
    ];
    return $statusMap[$statusNumber] ?? 'desconhecido';
}
?>