SELECT id, subject, assignee, status, created_at, category, sector, urgency
FROM tickets
WHERE created_by = ? AND status IN (3, 4)
ORDER BY created_at DESC
