# Environment Configuration Guide

## Quick Start

1. **Copy .env.example to .env:**
   ```bash
   copy .env.example .env
   ```

2. **Generate Application Key:**
   ```bash
   php artisan key:generate
   ```

3. **Configure Database:**
   - Open `.env` file
   - Update `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`

4. **Run Migrations:**
   ```bash
   php artisan migrate
   php artisan db:seed
   ```

---

## Core Configuration

### Application Settings
```env
APP_NAME="Project Management AI"
APP_ENV=production                # local|staging|production
APP_DEBUG=false                   # true for development only
APP_URL=https://your-domain.com
```

### Database
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database
DB_USERNAME=your_user
DB_PASSWORD=your_password
```

### Cache (Recommended: Redis)
```env
CACHE_STORE=redis
REDIS_HOST=127.0.0.1
REDIS_PORT=6379
REDIS_PASSWORD=null
```

---

## AI System Configuration

### 1. Core AI Settings

**Enable/Disable AI System:**
```env
AI_SYSTEM_ENABLED=true
```

**Default Provider:**
```env
AI_DEFAULT_PROVIDER=local
# Options: local | openai | claude
```

**Guardrails:**
```env
AI_MIN_CONFIDENCE=0.7              # Minimum confidence to show decision
AI_MAX_ACTIONS_PER_HOUR=100       # Rate limit for AI actions
AI_REQUIRE_APPROVAL_BELOW=0.8     # Require manual approval below this
```

---

### 2. External AI Providers (Optional)

#### OpenAI (GPT-4)
```env
OPENAI_API_KEY=sk-proj-...
OPENAI_MODEL=gpt-4
OPENAI_TIMEOUT=30
OPENAI_MAX_TOKENS=1000
```

**Get API Key:**
1. Visit https://platform.openai.com
2. Create account
3. Go to API Keys
4. Create new key

**Cost:** ~$0.03 per 1K tokens

#### Claude (Anthropic)
```env
CLAUDE_API_KEY=sk-ant-...
CLAUDE_MODEL=claude-3-sonnet-20240229
CLAUDE_TIMEOUT=30
CLAUDE_MAX_TOKENS=1000
```

**Get API Key:**
1. Visit https://console.anthropic.com
2. Create account
3. Generate API key

**Cost:** ~$0.015 per 1K tokens

---

### 3. Slack Integration

```env
SLACK_WEBHOOK_URL=https://hooks.slack.com/services/YOUR/WEBHOOK/URL
SLACK_NOTIFICATION_CHANNEL=#ai-notifications
SLACK_NOTIFICATION_USERNAME="AI Assistant"
```

**Setup:**
1. Create Slack app: https://api.slack.com/apps
2. Enable Incoming Webhooks
3. Add webhook to workspace
4. Copy webhook URL

---

### 4. Custom Webhooks

```env
AI_WEBHOOK_URL=https://your-domain.com/api/webhook
```

**Purpose:** Send AI decision notifications to external systems

---

### 5. Performance Settings

```env
# Cache
AI_CACHE_TTL=3600                  # Cache lifetime (seconds)
AI_CACHE_DRIVER=redis              # file | redis | memcached
AI_ENABLE_QUERY_CACHE=true

# Rate Limiting
AI_RATE_LIMIT=60                   # Requests per window
AI_RATE_LIMIT_WINDOW=60            # Window in seconds
```

**Recommended for Production:**
- Use Redis for caching
- Set `AI_CACHE_TTL=3600` (1 hour)
- Enable query cache

---

### 6. Automation Settings

```env
AI_AUTO_EXECUTE_ENABLED=false      # Enable auto-execution
AI_AUTO_EXECUTE_MIN_CONFIDENCE=0.95 # Only auto-execute if >= 95%
```

**⚠️ Warning:** Only enable auto-execution after thorough testing!

---

### 7. Feature Flags

```env
AI_ENABLE_LEARNING=true            # Enable AI learning from feedback
AI_ENABLE_ANALYTICS=true           # Enable analytics dashboard
AI_ENABLE_AUTOMATION=true          # Enable automation workflows
AI_ENABLE_EXTERNAL_PROVIDERS=false # Enable OpenAI/Claude
```

---

## Environment-Specific Configurations

### Local Development
```env
APP_ENV=local
APP_DEBUG=true
CACHE_STORE=file
AI_DEFAULT_PROVIDER=local
AI_ENABLE_EXTERNAL_PROVIDERS=false
```

### Staging
```env
APP_ENV=staging
APP_DEBUG=true
CACHE_STORE=redis
AI_DEFAULT_PROVIDER=local
AI_ENABLE_EXTERNAL_PROVIDERS=true  # For testing
```

### Production
```env
APP_ENV=production
APP_DEBUG=false
CACHE_STORE=redis
AI_DEFAULT_PROVIDER=openai         # If using external AI
AI_ENABLE_EXTERNAL_PROVIDERS=true
AI_AUTO_EXECUTE_ENABLED=false      # Human-in-the-loop recommended
```

---

## Security Best Practices

1. **Never commit .env to Git**
   - Already in `.gitignore`
   - Use environment variables in CI/CD

2. **Rotate API Keys Regularly**
   - OpenAI: Every 90 days
   - Claude: Every 90 days
   - Webhooks: Every 6 months

3. **Use Strong Database Passwords**
   - Minimum 16 characters
   - Mix of letters, numbers, symbols

4. **Enable HTTPS in Production**
   ```env
   APP_URL=https://your-domain.com
   ```

5. **Disable Debug in Production**
   ```env
   APP_DEBUG=false
   ```

---

## Testing Configuration

Create `.env.testing`:
```env
APP_ENV=testing
DB_DATABASE=project_management_test
CACHE_STORE=array
AI_SYSTEM_ENABLED=false
```

---

## Troubleshooting

### Issue: AI System Not Working
**Check:**
- `AI_SYSTEM_ENABLED=true`
- Database migrations run
- Permissions seeded

### Issue: OpenAI/Claude Not Responding
**Check:**
- Valid API key
- Sufficient credits
- Correct model name
- Network connectivity

### Issue: Slow Performance
**Solutions:**
- Enable Redis: `CACHE_STORE=redis`
- Increase cache TTL: `AI_CACHE_TTL=7200`
- Enable query cache: `AI_ENABLE_QUERY_CACHE=true`

### Issue: Slack Notifications Not Sending
**Check:**
- Webhook URL is correct
- URL starts with `https://hooks.slack.com`
- Test via Integrations dashboard

---

## Monitoring

**Recommended Environment Variables for Monitoring:**

```env
# Error Tracking (Sentry)
SENTRY_LARAVEL_DSN=
SENTRY_TRACES_SAMPLE_RATE=1.0

# Performance Monitoring
AI_LOG_SLOW_QUERIES=true
AI_SLOW_QUERY_THRESHOLD=1000  # milliseconds
```

---

## Cost Estimation

### Using Local Provider (Default)
- **Cost:** $0
- **Performance:** Fast
- **Accuracy:** Good for basic tasks

### Using OpenAI (GPT-4)
- **Cost:** ~$5-50/month (depending on usage)
- **Performance:** 1-3 seconds per request
- **Accuracy:** Excellent

### Using Claude
- **Cost:** ~$3-30/month
- **Performance:** 1-2 seconds per request
- **Accuracy:** Excellent

---

## Quick Reference

| Variable | Default | Production | Description |
|----------|---------|------------|-------------|
| `AI_SYSTEM_ENABLED` | `true` | `true` | Enable/disable AI |
| `AI_DEFAULT_PROVIDER` | `local` | `openai` | AI provider |
| `AI_MIN_CONFIDENCE` | `0.7` | `0.8` | Min confidence |
| `AI_CACHE_TTL` | `3600` | `7200` | Cache lifetime |
| `CACHE_STORE` | `file` | `redis` | Cache driver |
| `AI_AUTO_EXECUTE_ENABLED` | `false` | `false` | Auto-execution |

---

**Status:** Ready to configure
**Version:** 2.0.0
**Last Updated:** January 2026
