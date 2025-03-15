# Use Ubuntu as the base image
FROM debian:bookworm-slim

# Set environment variables to non-interactive (avoids some prompts)
ENV DEBIAN_FRONTEND=noninteractive

# Update and install dependencies
RUN apt-get update && apt-get install -y --no-install-recommends \
    sbcl \
    git \
    curl \
    make \
    socat \
    ca-certificates \
    && apt clean

# Set the working directory
WORKDIR /app

# Copy the project into the Docker container
COPY . /app

RUN curl -O https://beta.quicklisp.org/quicklisp.lisp && \
    sbcl --non-interactive --load quicklisp.lisp --eval "(quicklisp-quickstart:install)" --quit

RUN make live && \
    mkdir -p /opt/data/mathb/ /opt/log/mathb/ && \
    cp -R meta/data/* /opt/data/mathb/ && \
    chown -R "$(whoami)" /opt/data/mathb/ /opt/log/mathb/

# Expose the port the app runs on
EXPOSE 4343

# Command to run the application
ENTRYPOINT ["/bin/sh", "run.sh"]
