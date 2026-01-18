FROM liquibase/liquibase:5.0.1
RUN liquibase lpm add mysql --global
# Remove the convoluted entrypoint script, its directory changing behaviour 
# conflicts with the old workflow
ENTRYPOINT ["/liquibase/liquibase"]
