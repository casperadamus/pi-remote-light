# Pi Remote Light Switch

This project uses a Raspberry Pi and a motor to remotely control a physical light switch. The system is housed in a custom 3D-printed casing and is controlled via a web interface.

## Features

* **Remote Control:** Toggle a light switch from a web browser.
* **3D-Printed Casing:** A custom-designed case to house the Raspberry Pi and motor, mounted over the light switch.
* **Motorized Action:** A servo or stepper motor physically flips the switch.

---

## Hardware

* **Raspberry Pi:** (Specify your model, e.g., Raspberry Pi 4 Model B)
* **Motor:** (Specify your motor, e.g., SG90 servo motor)
* **3D-Printed Parts:**
    * Main Casing
    * Motor Mount
    * Switch Actuator Arm
* **Power Supply:** (e.g., 5V USB-C for the Pi)
* **Jumper Wires**

*(You would add your `.stl` or other 3D model files to a folder in this repository, perhaps named `3d-models/`)*

---

## Software & Code

This project is built in two main parts:

### 1. Web Application (Laravel)

The web interface is built using the Laravel PHP framework.

* **Framework:** Laravel
* **Key Files:**
    * `routes/web.php`: Defines the web routes (e.g., `/light/on`, `/light/off`) that trigger the action.
    * `app/Http/Controllers/LightController.php`: The controller that handles the web request and executes the Python script.
    * `resources/views/welcome.blade.php`: The simple web page with buttons to control the light.

### 2. Raspberry Pi Script (Python)

A Python script on the Raspberry Pi controls the motor connected to the GPIO pins.

* **Language:** Python
* **Key Libraries:**
    * `RPi.GPIO`: Used to control the Raspberry Pi's GPIO pins.
    * `time`: Used for delays to manage the motor's movement.
* **Script:** `motor_control.py` (This is an example name)
    * The script accepts arguments (e.g., `on` or `off`).
    * It activates the motor, rotating it to the correct position to flip the switch.
    * The Laravel application calls this script using a function like `shell_exec()`.

---

## Installation & Setup

1.  **3D Print:** Print all the components from the `3d-models/` directory.
2.  **Assemble Hardware:** Mount the Raspberry Pi and motor in the casing as designed.
3.  **Deploy Web App:**
    * Clone this repository onto your Raspberry Pi: `git clone https://github.com/casperadamus/pi-remote-light.git`
    * Install PHP, Composer, and a web server (like Nginx or Caddy).
    * Navigate to the project directory: `cd pi-remote-light`
    * Install dependencies: `composer install`
    * Set up your environment: `cp .env.example .env` and then `php artisan key:generate`
    * Configure your web server to point to the `public/` directory.
4.  **Setup Python Script:**
    * Make sure your Python script (e.g., `motor_control.py`) is executable: `chmod +x motor_control.py`
    * Ensure the web server user (e.g., `www-data`) has permission to execute the script and access GPIO pins.