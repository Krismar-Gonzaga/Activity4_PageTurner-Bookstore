<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Scheduled Export</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.5; color: #1f2937;">
    <h2 style="margin: 0 0 12px; color: #8B4513;">Scheduled Export Delivery</h2>
    <p>Hello Admin,</p>
    <p>Your scheduled export has been generated and attached to this email.</p>
    <ul>
        <li>Name: {{ $scheduledExport->name }}</li>
        <li>Type: {{ ucfirst(str_replace('_', ' ', $scheduledExport->type)) }}</li>
        <li>Schedule: {{ ucfirst($scheduledExport->schedule) }}</li>
        <li>Generated at: {{ $generatedAt }}</li>
    </ul>
    <p>Please check the attachment for the report file.</p>
</body>
</html>
