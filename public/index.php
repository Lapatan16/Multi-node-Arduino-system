<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Multi-node Arduino system</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f6f9;
            margin: 0;
            padding: 40px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        h1 {
            color: #2c3e50;
            margin-bottom: 30px;
        }
        .container {
            display: flex;
            gap: 20px;
            max-width: 800px;
            width: 100%;
        }
        .card {
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            flex: 1;
            text-align: center;
            border-top: 5px solid #3498db;
        }
        .card.teensy {
            border-top-color: #e74c3c;
        }
        .card h2 {
            margin: 0 0 5px 0;
            color: #34495e;
        }
        .sensor-model {
            font-size: 0.85rem;
            color: #95a5a6;
            margin-bottom: 15px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .voltage-display {
            font-size: 3rem;
            font-weight: bold;
            color: #2c3e50;
            margin: 10px 0;
        }
        .unit {
            font-size: 1.5rem;
            color: #7f8c8d;
        }
        .status {
            font-size: 1rem;
            font-weight: 500;
            color: #27ae60;
            margin-top: 15px;
        }
    </style>
</head>
<body>

    <h1>Multi-node Arduino system</h1>

    <div class="container">
        <div class="card esp">
            <h2>Flexibility Load</h2>
            <div class="sensor-model">ESP8266MOD</div>
            <div class="voltage-display"><span id="esp-val">0</span><span class="unit">%</span></div>
            <div class="status">● Flex Sensor Tracking</div>
        </div>

        <div class="card teensy">
            <h2>Applied Pressure</h2>
            <div class="sensor-model">TEENSY 3.6</div>
            <div class="voltage-display"><span id="teensy-val">0</span><span class="unit">%</span></div>
            <div class="status" style="color: #2980b9;">● Force Sensor Tracking</div>
        </div>
    </div>

    <script>
        const endpoint = 'http://192.168.4.1/api/data';

        function voltageToPercentage(voltage) {
            if (voltage < 0) voltage = 0;
            if (voltage > 3.3) voltage = 3.3;
            return (voltage / 3.3) * 100;
        }

        async function fetchSensorData() {
            try {
                const response = await fetch(endpoint);
                if (!response.ok) throw new Error('Network error response');
                
                const data = await response.json();
                
                const espPercent = voltageToPercentage(data.esp_voltage);
                const teensyPercent = voltageToPercentage(data.teensy_voltage);

                document.getElementById('esp-val').innerText = espPercent.toFixed(0);
                document.getElementById('teensy-val').innerText = teensyPercent.toFixed(0);
            } catch (error) {
                console.error('Error hitting ESP8266 API endpoint:', error);
            }
        }

        setInterval(fetchSensorData, 300);
        fetchSensorData();
    </script>
</body>
</html>