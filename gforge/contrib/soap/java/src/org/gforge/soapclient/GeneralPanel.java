package org.gforge.soapclient;

import javax.swing.JButton;
import javax.swing.JLabel;
import javax.swing.JPanel;
import javax.swing.JTree;
import javax.swing.border.LineBorder;
import javax.swing.border.TitledBorder;
import javax.swing.tree.TreeNode;
import javax.swing.tree.DefaultMutableTreeNode;
import javax.swing.tree.TreeModel;
import javax.swing.tree.DefaultTreeModel;
import java.awt.GridLayout;
import java.awt.Color;
import java.awt.BorderLayout;
import java.awt.event.ActionEvent;
import java.awt.event.ActionListener;

public class GeneralPanel extends JPanel {

    private class RefreshListener implements ActionListener {
        public void actionPerformed(ActionEvent e) {
            try {
                Client client = new Client(Settings.getInstance().get(ConfigurationPanel.SERVER_PROPERTY),
                        Settings.getInstance().get(ConfigurationPanel.GROUP_PROPERTY)
                        );
                userCount = String.valueOf(client.getNumberOfActiveUsers());
                projectCount = String.valueOf(client.getNumberOfHostedProjects());

                String[] projectNames = client.getPublicProjectNames();
                DefaultMutableTreeNode root = new DefaultMutableTreeNode(Settings.getInstance().get(ConfigurationPanel.SERVER_PROPERTY));
                for (int i=0;i<projectNames.length; i++) {
                    root.add(new DefaultMutableTreeNode(projectNames[i]));
                }
                projects = new DefaultTreeModel(root);

                refresh();
           }  catch (Exception ex) {
                ex.printStackTrace();
                userCount = "Can't contact server";
                projectCount = "Can't contact server";
                DefaultMutableTreeNode root = new DefaultMutableTreeNode(Settings.getInstance().get(ConfigurationPanel.SERVER_PROPERTY));
                root.add(new DefaultMutableTreeNode("Can't contact server"));
            }
        }
    }

    private String userCount = "Unknown";
    private String projectCount = "Unknown";
    private DefaultTreeModel projects = new DefaultTreeModel(new DefaultMutableTreeNode("Hit <refresh>"));

    public GeneralPanel() {
        super();
        refresh();
    }

    private void refresh() {
        this.removeAll();
        this.repaint();

        JPanel overallStatsPanel = new JPanel(new BorderLayout());
        overallStatsPanel.setBorder(new TitledBorder("Statistics"));
        JPanel projectsAndUsersCountPanel = new JPanel(new GridLayout(2,1));
        projectsAndUsersCountPanel.add(new JLabel("Projects: " + projectCount));
        projectsAndUsersCountPanel.add(new JLabel("Users: " + userCount));
        overallStatsPanel.add(projectsAndUsersCountPanel, BorderLayout.NORTH);

        JButton refreshButton = new JButton("Refresh");
        refreshButton.setMnemonic('r');
        refreshButton.addActionListener(new RefreshListener());
        JPanel refreshPanel = new JPanel();
        refreshPanel.add(refreshButton);
        overallStatsPanel.add(refreshPanel, BorderLayout.SOUTH);

        JPanel projectTreePanel = new JPanel(new BorderLayout());
        projectTreePanel.setBorder(new TitledBorder("Projects"));
        JTree projectTree = new JTree(projects);
        projectTree.setBackground(Color.GRAY);
        projectTreePanel.add(projectTree, BorderLayout.WEST);

        setLayout(new BorderLayout());
        add(overallStatsPanel, BorderLayout.NORTH);
        add(projectTreePanel, BorderLayout.CENTER);
    }
}
