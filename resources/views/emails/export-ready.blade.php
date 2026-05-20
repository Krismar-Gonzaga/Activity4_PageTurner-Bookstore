<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Export Ready</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.5; color: #1f2937;">
    <h2 style="margin: 0 0 12px; color: #8B4513;">Your export is ready</h2>
    <p>Hello,</p>
    <p>Your <strong>{{ ucfirst(str_replace('_', ' ', $export->export_type)) }}</strong> export has completed successfully.</p>
    <ul>
        <li>Format: {{ strtoupper($export->format) }}</li>
        <li>Total records: {{ $export->total_records ?? 0 }}</li>
        <li>Completed at: {{ optional($export->completed_at)->format('F j, Y g:i A') ?? now()->format('F j, Y g:i A') }}</li>
    </ul>
    <p>You can download this file from the export section in your admin panel.</p>
</body>
</html>
