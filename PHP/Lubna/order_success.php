:root {
  --bg1: #ffe7f5;
  --bg2: #d9b8ff;
  --bg3: #ffb7de;
  --card: rgba(255, 255, 255, 0.8);
  --card2: rgba(255, 255, 255, 0.6);
  --border: rgba(255, 255, 255, 0.9);
  --text: #2d2d2d;
  --muted: #6b6b6b;
  --title: #2f5a4c;
  --accent: #ff5fbf;
  --accent2: #8c64ff;
  --danger: #ff3b6b;
  --ok: #2bb673;
  --shadow: 0 20px 40px rgba(0, 0, 0, 0.06);
  --radius: 28px;
  --pill: 999px;
}

* { box-sizing: border-box; }

body {
  margin: 0;
  font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
  color: var(--text);
  min-height: 100vh;
  background: 
    radial-gradient(900px 600px at 12% 20%, var(--bg1), transparent 60%),
    radial-gradient(900px 600px at 88% 25%, var(--bg2), transparent 55%),
    radial-gradient(900px 600px at 70% 90%, var(--bg3), transparent 60%),
    linear-gradient(135deg, #ffeaf7, #e4c8ff, #ffc7e4);
  background-attachment: fixed;
}

.checkoutWrap {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 30px;
  width: min(1000px, 94%);
  margin: 50px auto;
  align-items: start;
}

@media (max-width: 850px) {
  .checkoutWrap { grid-template-columns: 1fr; }
}

.checkoutCard {
  background: var(--card);
  border: 1.5px solid var(--border);
  border-radius: var(--radius);
  padding: 35px;
  box-shadow: var(--shadow);
  backdrop-filter: blur(10px);
}

.checkoutTitle {
  font-size: 1.3rem;
  font-weight: 800;
  color: var(--title);
  margin-bottom: 20px;
}

.form-group {
  display: flex;
  flex-direction: column;
  margin-bottom: 18px;
}

.form-group label {
  font-weight: 700;
  margin-bottom: 8px;
  font-size: 14px;
  color: #444;
}

.form-group input {
  padding: 14px 18px;
  border-radius: var(--pill);
  border: 1.5px solid #fff;
  background: rgba(255, 255, 255, 0.9);
  font-size: 15px;
  transition: all 0.3s ease;
  box-shadow: inset 0 2px 4px rgba(0,0,0,0.02);
}

.form-group input:focus {
  outline: none;
  border-color: var(--accent);
  background: #fff;
  box-shadow: 0 0 0 4px rgba(255, 95, 191, 0.1);
}

.orderTable {
  width: 100%;
  border-collapse: collapse;
  margin-bottom: 20px;
}

.orderTable th {
  padding: 12px;
  background: rgba(255,255,255,0.5);
  font-weight: 700;
  text-align: center;
  border-bottom: 2px solid var(--border);
}

.orderTable td {
  padding: 14px 10px;
  text-align: center;
  border-bottom: 1px solid rgba(255,255,255,0.4);
}

.orderTable td:first-child { text-align: left; font-weight: 600; }

.orderTotalRow {
  display: flex;
  justify-content: space-between;
  padding: 15px 20px;
  background: rgba(255,255,255,0.6);
  border-radius: 18px;
  font-weight: 800;
  font-size: 1.1rem;
}

.buyBtn {
  display: block;
  width: 100%;
  margin-top: 30px;
  padding: 18px;
  border: none;
  border-radius: var(--pill);
  background: linear-gradient(90deg, var(--accent), var(--accent2));
  color: #fff;
  font-size: 1.1rem;
  font-weight: 800;
  cursor: pointer;
  box-shadow: 0 10px 25px rgba(255,95,191,0.4);
  transition: all 0.3s ease;
}

.buyBtn:hover {
  transform: translateY(-3px);
  box-shadow: 0 15px 30px rgba(255,95,191,0.5);
  opacity: 0.95;
}

.buyBtn:disabled {
  opacity: 0.5;
  cursor: not-allowed;
  transform: none;
}

.btn-update, .btn-remove {
  padding: 6px 10px;
  border-radius: 10px;
  cursor: pointer;
  font-weight: bold;
  border: 1px solid transparent;
}

.btn-update { background: #e8f5e9; color: #2e7d32; border-color: #c8e6c9; }
.btn-remove { background: #ffebee; color: #c62828; border-color: #ffcdd2; }

.qty-input {
  width: 50px;
  padding: 6px;
  border-radius: 10px;
  border: 1px solid #ddd;
  text-align: center;
}

.backToCart {
  display: block;
  text-align: center;
  margin-top: 20px;
  color: var(--muted);
  text-decoration: none;
  font-weight: 700;
  font-size: 14px;
}

.backToCart:hover { color: var(--accent); }

.checkout-error {
  color: var(--danger);
  background: rgba(255,59,107,0.1);
  padding: 12px;
  border-radius: 12px;
  margin-bottom: 20px;
  font-weight: 600;
  text-align: center;
}

.btn-link-danger {
  background: none;
  border: 1.5px solid var(--danger);
  color: var(--danger);
  border-radius: var(--pill);
  padding: 6px 15px;
  cursor: pointer;
  font-weight: 700;
}
:root {
  --bg1: #ffe7f5;
  --bg2: #d9b8ff;
  --bg3: #ffb7de;
  --card: rgba(255, 255, 255, 0.8);
  --card2: rgba(255, 255, 255, 0.6);
  --border: rgba(255, 255, 255, 0.9);
  --text: #2d2d2d;
  --title: #2f5a4c;
  --accent: #ff5fbf;
  --accent2: #8c64ff;
  --ok: #2bb673;
  --shadow: 0 20px 40px rgba(0, 0, 0, 0.06);
  --radius: 28px;
  --pill: 999px;
}

* { box-sizing: border-box; }

body {
  margin: 0;
  font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
  color: var(--text);
  min-height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  background: 
    radial-gradient(900px 600px at 12% 20%, var(--bg1), transparent 60%),
    radial-gradient(900px 600px at 88% 25%, var(--bg2), transparent 55%),
    radial-gradient(900px 600px at 70% 90%, var(--bg3), transparent 60%),
    linear-gradient(135deg, #ffeaf7, #e4c8ff, #ffc7e4);
  background-attachment: fixed;
}

.container.success {
  width: min(550px, 92%);
  background: var(--card);
  border: 1.5px solid var(--border);
  border-radius: var(--radius);
  padding: 50px 40px;
  box-shadow: var(--shadow);
  backdrop-filter: blur(15px);
  text-align: center;
  animation: fadeIn 0.8s ease-out;
}

@keyframes fadeIn {
  from { opacity: 0; transform: translateY(20px); }
  to { opacity: 1; transform: translateY(0); }
}

.container h1 {
  font-size: 2.2rem;
  font-weight: 800;
  color: var(--title);
  margin-bottom: 10px;
}

.container h2 {
  font-size: 1.5rem;
  color: var(--ok);
  margin-bottom: 20px;
  font-weight: 700;
}

.container p {
  font-size: 1.1rem;
  color: #555;
  line-height: 1.6;
  margin: 10px 0;
}

.container strong {
  color: var(--accent2);
  font-size: 1.2rem;
  background: rgba(140, 100, 255, 0.1);
  padding: 2px 10px;
  border-radius: 8px;
}

.btn-primary {
  display: inline-block;
  margin-top: 30px;
  padding: 16px 35px;
  border: none;
  border-radius: var(--pill);
  background: linear-gradient(90deg, var(--accent), var(--accent2));
  color: #fff;
  font-size: 1.1rem;
  font-weight: 800;
  text-decoration: none;
  cursor: pointer;
  box-shadow: 0 10px 25px rgba(255, 95, 191, 0.4);
  transition: all 0.3s ease;
}

.btn-primary:hover {
  transform: translateY(-3px);
  box-shadow: 0 15px 30px rgba(255, 95, 191, 0.5);
  opacity: 0.95;
}

.success-icon {
  font-size: 50px;
  margin-bottom: 20px;
  display: block;
}
