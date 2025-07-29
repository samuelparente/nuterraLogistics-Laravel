#!/bin/bash

# Configuração
REMOTO_BACKUP="github_backup"

# 🎯 Detectar branch atual
BRANCH_ATUAL=$(git rev-parse --abbrev-ref HEAD)
echo
echo "📍 Branch atual: $BRANCH_ATUAL"

# 🔖 Obter última tag (se existir)
ULTIMA_TAG=$(git describe --tags --abbrev=0 2>/dev/null)

echo
if [[ -n "$ULTIMA_TAG" ]]; then
    echo "📌 Última versão existente: $ULTIMA_TAG"
else
    echo "ℹ️ Ainda não existem versões com tag."
fi

# 🔖 Perguntar versão nova (qualquer formato)
read -p "🔖 Introduz o nome da nova versão/tag (ex: v1.2.4 ou release-julho): " VERSAO
VERSAO=$(echo "$VERSAO" | xargs)  # Limpar espaços

# 💾 Commit local com mensagem
echo
echo "💾 A guardar alterações locais (se existirem)..."
git add .

read -p "📝 Mensagem do commit (enter para padrão): " MENSAGEM_COMMIT
if [[ -z "$MENSAGEM_COMMIT" ]]; then
    MENSAGEM_COMMIT="Preparação para release $VERSAO ($BRANCH_ATUAL)"
fi

git commit -m "$MENSAGEM_COMMIT"
if [[ $? -ne 0 ]]; then
    echo "⚠️  Sem alterações para commitar ou erro ao commitar. A continuar..."
fi

# 🚀 Push da branch atual
echo
echo "🚀 A enviar $BRANCH_ATUAL para origin e $REMOTO_BACKUP..."
git push origin "$BRANCH_ATUAL"
git push "$REMOTO_BACKUP" "$BRANCH_ATUAL"

# 🔁 Merge para develop
echo
read -p "🔁 Queres fazer merge de $BRANCH_ATUAL para 'develop'? (s/n): " MERGE_DEVELOP
if [[ "$MERGE_DEVELOP" =~ ^[Ss]$ ]]; then
    echo "📥 A mudar para develop..."
    git checkout develop || { echo "❌ Erro ao mudar para develop."; exit 1; }

    echo "🔧 A fazer merge..."
    git merge --no-ff "$BRANCH_ATUAL" -m "Merge de $BRANCH_ATUAL para develop (versão $VERSAO)"
    if [[ $? -ne 0 ]]; then
        echo "❌ Erro no merge para develop."
        exit 1
    fi

    echo "📤 A enviar develop para os remotos..."
    git push origin develop
    git push "$REMOTO_BACKUP" develop
else
    echo "⏭️  Merge para develop ignorado."
fi

# 🏁 Merge para main e tag
echo
read -p "🏁 Queres fazer merge de 'develop' para 'main' e criar a tag $VERSAO? (s/n): " MERGE_MAIN
if [[ "$MERGE_MAIN" =~ ^[Ss]$ ]]; then
    echo "📥 A mudar para main..."
    git checkout main || { echo "❌ Erro ao mudar para main."; exit 1; }

    echo "🔧 A fazer merge..."
    git merge --no-ff develop -m "Release $VERSAO - Merge de develop para main"
    if [[ $? -ne 0 ]]; then
        echo "❌ Erro ao fazer merge para main."
        read -p "⏪ Queres desfazer o merge local na main? (s/n): " ROLLBACK
        if [[ "$ROLLBACK" =~ ^[Ss]$ ]]; then
            git merge --abort 2>/dev/null
            git reset --hard HEAD
            echo "🔄 Merge desfeito. Estado restaurado."
        else
            echo "ℹ️  O merge não foi revertido. Verifica manualmente."
        fi
        exit 1
    fi

    echo "🏷️  A criar tag $VERSAO..."
    git tag -a "$VERSAO" -m "Versão $VERSAO publicada com merge de develop para main"

    echo "📤 A enviar main e tags para os remotos..."
    git push origin main
    git push origin --tags
    git push "$REMOTO_BACKUP" main
    git push "$REMOTO_BACKUP" --tags

    echo
    echo "✅ Release $VERSAO concluída com sucesso!"
else
    echo "⏭️  Merge para main ignorado."
fi

echo
read -p "Pressiona ENTER para sair..."
