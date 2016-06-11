<%@ page import="java.sql.*,java.io.*" %>
<%
	char[] code = new char[5];
	String opr = request.getParameter("opr");

	response.setHeader("Access-Control-Allow-Origin", "*");
	
	try
	{
		Class.forName("com.mysql.jdbc.Driver");
		
		Connection conn = DriverManager.getConnection(
			"jdbc:mysql://localhost/mastermind", "root", null);
		
		// The connection to mysql is setup
		
		if(opr.equals("guess") || opr.equals("update")) 
		{
			//Game is in progress, and the user has a guess...
			String user = request.getParameter("username");
			String gid = request.getParameter("gid");
			String guess = request.getParameter("g");
			
			Statement stmt = conn.createStatement();
			ResultSet result = conn.prepareStatement("SELECT * FROM games WHERE game_id='"+gid+"'" ).executeQuery();
			if(result.next())
			{
				String c = result.getString("code_to_guess");
				code[0] = c.charAt(0);
				code[1] = c.charAt(2);
				code[2] = c.charAt(4);
				
				Integer n = 0;	//number of correct guesses
				for(Integer i = 0; i < 3; i++)
				{
					if(guess.charAt(i) == code[i])
					{
						n++;
					}
				}
				
				if(opr.equals("guess"))
				{
					String str = "With "+guess.charAt(0)+";"+guess.charAt(1)+";"+guess.charAt(2)+", "+user+" guessed "+n+" colour(s). ";
					String sql = "UPDATE games SET results = CONCAT(results,'"+str+"') WHERE game_id='"+gid+"'";
					PreparedStatement stmt1 = conn.prepareStatement(sql);
					int result1 = stmt1.executeUpdate(sql); // number of records updated
					if(result1 > 0)
					{
						//out.print("ok");
					}
					else	
					{
						//out.print("No record update");
					}
				}
				Statement stmt2 = conn.createStatement();
				ResultSet result2 = conn.prepareStatement("SELECT * FROM games WHERE game_id='"+gid+"'" ).executeQuery();
				if(result2.next())
				{
					String r = result2.getString("results");
					out.print(r);
				}
				else
				{
					//out.print("Did not find any record with "+gid);
				}
			}
			else
			{
				//out.print("No record update");
			}
		}
		
		if(opr.equals("check")) 
		{
			String user = request.getParameter("username");
			String gid = request.getParameter("gid");
			
			Statement stmt = conn.createStatement();
			ResultSet result = conn.prepareStatement("SELECT * FROM games WHERE game_id='"+gid+"'" ).executeQuery();
			if(result.next())
			{
				String r = result.getString("results");
				if(r == "")
				{
					out.print("");
				}
				else{
					out.print(r);
				}
			}
		}
	}
	catch(Exception e)
	{
		out.print("error " + e.getMessage());
	}
%>
