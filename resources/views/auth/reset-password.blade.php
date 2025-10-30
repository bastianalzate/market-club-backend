<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restablecer Contraseña - Market Club</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Arial', sans-serif;
            background: white;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .container {
            background: white;
            border-radius: 15px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            max-width: 450px;
            width: 100%;
        }
        
        .header {
            background: linear-gradient(135deg, #B58E31 0%, #D4AF37 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        
        .header h1 {
            font-size: 28px;
            margin-bottom: 10px;
        }
        
        .header p {
            opacity: 0.9;
            font-size: 16px;
        }
        
        .form-container {
            padding: 40px 30px;
        }
        
        .form-group {
            margin-bottom: 25px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #333;
        }
        
        .form-group input {
            width: 100%;
            padding: 15px;
            border: 2px solid #e1e5e9;
            border-radius: 10px;
            font-size: 16px;
            transition: border-color 0.3s;
        }
        
        .form-group input:focus {
            outline: none;
            border-color: #B58E31;
        }
        
        .password-strength {
            margin-top: 8px;
            font-size: 14px;
            color: #666;
        }
        
        .strength-bar {
            height: 4px;
            background: #e1e5e9;
            border-radius: 2px;
            margin-top: 5px;
            overflow: hidden;
        }
        
        .strength-fill {
            height: 100%;
            transition: width 0.3s, background-color 0.3s;
            width: 0%;
        }
        
        .strength-weak { background-color: #e74c3c; }
        .strength-medium { background-color: #f39c12; }
        .strength-strong { background-color: #27ae60; }
        
        .btn {
            width: 100%;
            padding: 15px;
            background: linear-gradient(135deg, #B58E31 0%, #D4AF37 100%);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(181, 142, 49, 0.3);
        }
        
        .btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }
        
        .alert {
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
        }
        
        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .alert-error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .back-link {
            text-align: center;
            margin-top: 20px;
        }
        
        .back-link a {
            color: #B58E31;
            text-decoration: none;
            font-weight: 600;
        }
        
        .back-link a:hover {
            text-decoration: underline;
        }
        
        .loading {
            display: none;
            text-align: center;
            margin-top: 20px;
        }
        
        .spinner {
            border: 3px solid #f3f3f3;
            border-top: 3px solid #B58E31;
            border-radius: 50%;
            width: 30px;
            height: 30px;
            animation: spin 1s linear infinite;
            margin: 0 auto;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🍺 Market Club</h1>
            <p>Restablecer Contraseña</p>
        </div>
        
        <div class="form-container">
            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif
            
            @if(session('error'))
                <div class="alert alert-error">
                    {{ session('error') }}
                </div>
            @endif
            
            @if($errors->any())
                <div class="alert alert-error">
                    <ul style="margin: 0; padding-left: 20px;">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            
            <form id="resetForm" method="POST" action="{{ route('password.reset') }}">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">
                
                <div class="form-group">
                    <label for="password">Nueva Contraseña</label>
                    <input 
                        type="password" 
                        id="password" 
                        name="password" 
                        required 
                        minlength="8"
                        placeholder="Mínimo 8 caracteres"
                    >
                    <div class="password-strength">
                        <div class="strength-bar">
                            <div class="strength-fill" id="strengthFill"></div>
                        </div>
                        <span id="strengthText">Ingresa una contraseña</span>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="password_confirmation">Confirmar Contraseña</label>
                    <input 
                        type="password" 
                        id="password_confirmation" 
                        name="password_confirmation" 
                        required 
                        minlength="8"
                        placeholder="Repite tu contraseña"
                    >
                </div>
                
                <button type="submit" class="btn" id="submitBtn">
                    Restablecer Contraseña
                </button>
                
                <div class="loading" id="loading">
                    <div class="spinner"></div>
                    <p>Procesando...</p>
                </div>
            </form>
            
            <div class="back-link">
                <a href="{{ config('app.frontend_url') }}">← Volver al sitio</a>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const passwordInput = document.getElementById('password');
            const confirmInput = document.getElementById('password_confirmation');
            const strengthFill = document.getElementById('strengthFill');
            const strengthText = document.getElementById('strengthText');
            const form = document.getElementById('resetForm');
            const submitBtn = document.getElementById('submitBtn');
            const loading = document.getElementById('loading');
            
            // Validación de fortaleza de contraseña
            passwordInput.addEventListener('input', function() {
                const password = this.value;
                const strength = calculatePasswordStrength(password);
                
                strengthFill.style.width = strength.percentage + '%';
                strengthFill.className = 'strength-fill strength-' + strength.level;
                strengthText.textContent = strength.text;
            });
            
            // Validación de confirmación de contraseña
            confirmInput.addEventListener('input', function() {
                const password = passwordInput.value;
                const confirm = this.value;
                
                if (confirm && password !== confirm) {
                    this.setCustomValidity('Las contraseñas no coinciden');
                } else {
                    this.setCustomValidity('');
                }
            });
            
            // Envío del formulario
            form.addEventListener('submit', function(e) {
                const password = passwordInput.value;
                const confirm = confirmInput.value;
                
                if (password !== confirm) {
                    e.preventDefault();
                    alert('Las contraseñas no coinciden');
                    return;
                }
                
                if (password.length < 8) {
                    e.preventDefault();
                    alert('La contraseña debe tener al menos 8 caracteres');
                    return;
                }
                
                // Mostrar loading
                submitBtn.style.display = 'none';
                loading.style.display = 'block';
            });
            
            function calculatePasswordStrength(password) {
                let score = 0;
                let feedback = [];
                
                if (password.length >= 8) score += 1;
                else feedback.push('Mínimo 8 caracteres');
                
                if (/[a-z]/.test(password)) score += 1;
                else feedback.push('Incluye letras minúsculas');
                
                if (/[A-Z]/.test(password)) score += 1;
                else feedback.push('Incluye letras mayúsculas');
                
                if (/[0-9]/.test(password)) score += 1;
                else feedback.push('Incluye números');
                
                if (/[^A-Za-z0-9]/.test(password)) score += 1;
                else feedback.push('Incluye símbolos');
                
                if (score <= 2) {
                    return { level: 'weak', percentage: 33, text: 'Débil - ' + feedback.join(', ') };
                } else if (score <= 3) {
                    return { level: 'medium', percentage: 66, text: 'Media - ' + feedback.join(', ') };
                } else {
                    return { level: 'strong', percentage: 100, text: 'Fuerte - ¡Excelente contraseña!' };
                }
            }
        });
    </script>
</body>
</html>
