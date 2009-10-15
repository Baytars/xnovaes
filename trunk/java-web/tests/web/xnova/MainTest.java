package web.xnova;

import static org.junit.Assert.*;

import java.net.URL;
import java.util.Collections;
import java.io.File;
import java.io.BufferedReader;
import java.io.InputStreamReader;

import org.glassfish.config.support.GlassFishConfigBean;
import org.glassfish.embed.*;
import org.glassfish.javaee.*;
import org.junit.*;
import org.junit.Assert.*;
import org.mvc.Dispatcher;


import web.xnova.forms.LoginForm;

public class MainTest {
	
	private static final String WEB_CONTEXT = "web";
	
	private String outputData;
	
	@Before public void setUp() throws Exception {
		this.outputData = new String();
		
		int port = 8185;
	    GlassFish glassfish = newGlassFish(port);
	    URL url = new URL("http://xnova.test:" + port + "/" + WEB_CONTEXT + "/run-tests");
	    BufferedReader br = new BufferedReader(
	            new InputStreamReader(
	            url.openConnection().getInputStream()));
	    String line = null;
	    while ( null != ( line = br.readLine() ) ) {
	    	this.outputData = this.outputData.concat(line);
	    }
	    
	    glassfish.stop();
	}
	
 	@Test public void testFormExists() throws Exception {
 		assertTrue( this.outputData.contains( new LoginForm().render() ) );
 	}
	
	private GlassFish newGlassFish(int port) throws Exception {
	    GlassFish glassfish = new GlassFish(port);
	    
	    ScatteredWar war = new ScatteredWar( WEB_CONTEXT,
	            new File("test/webapp"),
	            new File("test/webapp/WEB-INF/web.xml"),
	            Collections.singleton(new File("build/xnova-build").toURI().toURL()));
	    glassfish.deploy(war);
	    return glassfish;
	}
}