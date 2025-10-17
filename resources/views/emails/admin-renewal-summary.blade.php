<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resumen de Renovaciones - Market Club Admin</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 700px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f4f4f4;
        }
        .container {
            background-color: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .content {
            padding: 30px;
        }
        .stats-grid {
            display: flex;
            gap: 15px;
            margin: 20px 0;
        }
        .stat-card {
            flex: 1;
            padding: 20px;
            border-radius: 8px;
            text-align: center;
        }
        .stat-success {
            background-color: #d4edda;
            border-left: 4px solid #28a745;
        }
        .stat-danger {
            background-color: #f8d7da;
            border-left: 4px solid #dc3545;
        }
        .stat-warning {
            background-color: #fff3cd;
            border-left: 4px solid #ffc107;
        }
        .stat-number {
            font-size: 36px;
            font-weight: bold;
            margin: 10px 0;
        }
        .stat-label {
            color: #666;
            font-size: 14px;
            text-transform: uppercase;
        }
        .summary-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        .summary-table th,
        .summary-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        .summary-table th {
            background-color: #f8f9fa;
            font-weight: bold;
        }
        .footer {
            text-align: center;
            padding: 20px;
            color: #666;
            font-size: 12px;
            background-color: #f8f9fa;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>📊 Resumen de Renovaciones Diarias</h1>
            <p style="margin: 5px 0 0 0; opacity: 0.9;">{{ $summary['date'] }}</p>
        </div>
        
        <div class="content">
            <h2>Estadísticas del Día</h2>
            
            <div class="stats-grid">
                <div class="stat-card stat-success">
                    <div class="stat-label">Procesadas</div>
                    <div class="stat-number" style="color: #28a745;">{{ $summary['processed'] }}</div>
                    <div>Exitosas</div>
                </div>
                
                <div class="stat-card stat-danger">
                    <div class="stat-label">Fallidas</div>
                    <div class="stat-number" style="color: #dc3545;">{{ $summary['failed'] }}</div>
                    <div>Con Errores</div>
                </div>
                
                <div class="stat-card stat-warning">
                    <div class="stat-label">Saltadas</div>
                    <div class="stat-number" style="color: #ffc107;">{{ $summary['skipped'] }}</div>
                    <div>Sin Procesar</div>
                </div>
            </div>
            
            <table class="summary-table">
                <thead>
                    <tr>
                        <th>Métrica</th>
                        <th>Valor</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><strong>Total Procesadas</strong></td>
                        <td>{{ $summary['processed'] + $summary['failed'] + $summary['skipped'] }}</td>
                    </tr>
                    <tr>
                        <td><strong>Tasa de Éxito</strong></td>
                        <td>
                            @php
                                $total = $summary['processed'] + $summary['failed'];
                                $successRate = $total > 0 ? round(($summary['processed'] / $total) * 100, 2) : 0;
                            @endphp
                            {{ $successRate }}%
                        </td>
                    </tr>
                    <tr>
                        <td><strong>Renovaciones Exitosas</strong></td>
                        <td>{{ $summary['processed'] }}</td>
                    </tr>
                    <tr>
                        <td><strong>Pagos Fallidos</strong></td>
                        <td>{{ $summary['failed'] }}</td>
                    </tr>
                    <tr>
                        <td><strong>Suscripciones Saltadas</strong></td>
                        <td>{{ $summary['skipped'] }}</td>
                    </tr>
                </tbody>
            </table>
            
            @if($summary['failed'] > 0)
                <div style="background-color: #fff3cd; padding: 15px; border-left: 4px solid #ffc107; margin: 20px 0;">
                    <h3 style="margin-top: 0;">⚠️ Atención Requerida</h3>
                    <p>Hay <strong>{{ $summary['failed'] }}</strong> suscripción(es) con fallos de pago que requieren revisión.</p>
                    <p style="margin-bottom: 0;">Por favor, revisa los logs para más detalles.</p>
                </div>
            @endif
            
            @if($summary['processed'] > 0)
                <div style="background-color: #d4edda; padding: 15px; border-left: 4px solid #28a745; margin: 20px 0;">
                    <h3 style="margin-top: 0;">✅ Operación Exitosa</h3>
                    <p style="margin-bottom: 0;">Se procesaron <strong>{{ $summary['processed'] }}</strong> renovación(es) exitosamente.</p>
                </div>
            @endif
            
            <p style="margin-top: 30px; color: #666; font-size: 14px;">
                Este es un resumen automático del proceso de renovaciones diarias. 
                Para más información detallada, revisa los logs del sistema.
            </p>
        </div>
        
        <div class="footer">
            <p>&copy; {{ date('Y') }} Market Club - Panel de Administración</p>
            <p>Sistema de Pagos Recurrentes</p>
        </div>
    </div>
</body>
</html>

